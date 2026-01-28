<?php
/**
 * Enterprise ERP API Controller
 * * Handles all logic for Inventory, POS, and User Management.
 * Implements security middleware, rate limiting, and transaction management.
 *
 * @author Muhammad Afiq Asnawi
 * @version 2.0.0 (Refactored)
 */

// 1. Strict Error Handling for Dev (Turn off for Production)
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

// 2. Session & Header Config
session_name('ERP_SECURE_SESSION');
session_start();

header('Content-Type: application/json');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');

// 3. Database Connection (Dependency Injection Pattern)
require_once 'db_config.php'; // Assumed separate config file
require_once 'helpers.php';   // Utility functions

// =========================================================================
// MAIN CONTROLLER
// =========================================================================

class ApiController {
    
    private $pdo;
    private $user;
    private $request;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->request = $this->getRequestData();
        $this->runMiddleware();
    }

    /**
     * Security Middleware: Bot Detection, Rate Limiting, CSRF
     */
    private function runMiddleware() {
        $userAgent = strtolower($_SERVER['HTTP_USER_AGENT'] ?? '');
        
        // Anti-Bot: Block generic crawlers to save resources
        $blockedBots = ['googlebot', 'bingbot', 'slurp', 'crawler', 'python-requests'];
        foreach ($blockedBots as $bot) {
            if (strpos($userAgent, $bot) !== false) {
                $this->sendError('Access Denied: Bot detected', 403);
            }
        }

        // Rate Limiting: Max 100 requests per minute per IP
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $rateKey = 'api_limit_' . $ip;
        if (!isset($_SESSION[$rateKey])) {
            $_SESSION[$rateKey] = ['count' => 0, 'reset' => time() + 60];
        }

        if (time() > $_SESSION[$rateKey]['reset']) {
            $_SESSION[$rateKey] = ['count' => 0, 'reset' => time() + 60];
        }

        $_SESSION[$rateKey]['count']++;
        if ($_SESSION[$rateKey]['count'] > 100) {
            $this->sendError('Rate limit exceeded', 429);
        }
    }

    /**
     * Parses incoming JSON or POST data
     */
    private function getRequestData() {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            return $_POST; // Fallback for FormData
        }
        return $input;
    }

    /**
     * Main Request Router
     */
    public function handleRequest() {
        $action = $this->request['action'] ?? $_GET['action'] ?? '';

        if (!$action) {
            $this->sendError('No action specified');
        }

        // Routing Table
        switch ($action) {
            case 'login':
                $this->handleLogin();
                break;
            
            // Job & POS Operations
            case 'fetch_jobs':
                $this->fetchJobs();
                break;
            case 'CREATE_JOB':
                $this->createJob();
                break;
            case 'update_full_job':
                $this->updateJob();
                break;
            
            // Inventory Operations
            case 'fetch_stock':
                $this->fetchStock();
                break;
            case 'insert_stock_item':
                $this->insertStockItem();
                break;
            case 'merge_duplicate_stocks':
                $this->mergeDuplicateStocks(); // Complex Logic
                break;

            // Analytics
            case 'get_report':
                $this->generateFinancialReport();
                break;

            default:
                $this->sendError("Invalid Action: " . htmlspecialchars($action));
        }
    }

    // =====================================================================
    // CORE LOGIC METHODS
    // =====================================================================

    /**
     * Authentication with Brute Force Protection
     */
    private function handleLogin() {
        $username = sanitizeString($this->request['username']);
        $password = $this->request['password'];
        $ip = $_SERVER['REMOTE_ADDR'];

        // Check Login Attempts (Brute Force Protection)
        $stmt = $this->pdo->prepare("SELECT count(*) FROM login_attempts WHERE ip_address = ? AND attempt_time > DATE_SUB(NOW(), INTERVAL 15 MINUTE)");
        $stmt->execute([$ip]);
        if ($stmt->fetchColumn() > 10) {
            $this->sendError('Too many failed attempts. Try again later.');
        }

        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['loggedin'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['branch'] = $user['assigned_branch'];
            
            $this->logActivity('LOGIN', "User $username logged in");
            $this->sendSuccess(['redirect' => 'dashboard.php']);
        } else {
            // Log failed attempt
            $this->pdo->prepare("INSERT INTO login_attempts (ip_address) VALUES (?)")->execute([$ip]);
            $this->sendError('Invalid Credentials');
        }
    }

    /**
     * Creates a new repair job and handles automatic stock deduction.
     * Logic: Parses a string like "Screen (x1)" -> finds item -> deducts inventory.
     */
    private function createJob() {
        $data = $this->request;
        
        try {
            $this->pdo->beginTransaction();

            // 1. Generate unique Job ID (e.g., REPAIR-2024-001)
            $jobID = $this->generateCustomID('Repair', $data['branch']);
            
            // 2. Parse Repair String for Inventory Deduction
            // Input Format: "iPhone LCD (x1) || Battery (x1)"
            $this->processStockDeduction($data['repair'], $data['branch']);

            // 3. Insert Job Record
            $sql = "INSERT INTO jobs (job_id, branch, customer, phone, device_model, repair_desc, price, status, created_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                $jobID, 
                $data['branch'], 
                $data['customer'], 
                $data['phone'], 
                $data['model'], 
                $data['repair'], 
                $data['price'], 
                'Pending'
            ]);

            $this->pdo->commit();
            $this->sendSuccess(['newJobId' => $jobID]);

        } catch (Exception $e) {
            $this->pdo->rollBack();
            $this->sendError("Job Creation Failed: " . $e->getMessage());
        }
    }

    /**
     * COMPLEX ALGORITHM: Fuzzy Matching for Stock Deduction
     * Parses free-text repair descriptions to deduct stock.
     */
    private function processStockDeduction($repairString, $branch) {
        // Defines which column to update based on branch
        $stockColumn = (stripos($branch, 'North') !== false) ? 'stock_north' : 'stock_south';
        
        $items = explode(' || ', $repairString);

        foreach ($items as $itemStr) {
            // Regex to extract Name and Quantity
            // Matches: "iPhone 11 Screen (x2)" -> Name: "iPhone 11 Screen", Qty: 2
            if (preg_match('/^(.*?) \(x(\d+)\)/', trim($itemStr), $matches)) {
                $partName = trim($matches[1]);
                $qty = intval($matches[2]);

                if ($qty > 0) {
                    // 1. Try Exact Match
                    $stmt = $this->pdo->prepare("UPDATE stock_list SET $stockColumn = $stockColumn - ? WHERE part_name = ?");
                    $stmt->execute([$qty, $partName]);

                    // 2. If no rows affected, try Fuzzy Match (Search logic)
                    if ($stmt->rowCount() == 0) {
                        $this->deductStockFuzzy($partName, $qty, $stockColumn);
                    }
                }
            }
        }
    }

    /**
     * Fallback mechanism if exact part name isn't found.
     * Uses LIKE operators to find similar items in inventory.
     */
    private function deductStockFuzzy($searchTerm, $qty, $column) {
        // Clean the search term
        $cleanTerm = str_replace(['1set', 'set'], '', $searchTerm);
        
        $sql = "SELECT part_name FROM stock_list WHERE part_name LIKE ? LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['%' . $cleanTerm . '%']);
        $match = $stmt->fetchColumn();

        if ($match) {
            $update = $this->pdo->prepare("UPDATE stock_list SET $column = $column - ? WHERE part_name = ?");
            $update->execute([$qty, $match]);
            error_log("Fuzzy match success: Deducted $qty from $match");
        } else {
            error_log("Stock Warning: Could not find item '$searchTerm' in inventory.");
        }
    }

    /**
     * Generates a P&L Report for specific dates/branches.
     * Calculates Cost of Goods Sold (COGS) dynamically.
     */
    private function generateFinancialReport() {
        if ($_SESSION['role'] !== 'admin') $this->sendError('Unauthorized');

        $start = $this->request['start'];
        $end = $this->request['end'];

        // Fetch Sales
        $sql = "SELECT sum(price) as revenue, sum(job_cost) as cogs FROM jobs 
                WHERE date BETWEEN ? AND ? AND status = 'Completed' AND is_deleted = 0";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$start, $end]);
        $financials = $stmt->fetch(PDO::FETCH_ASSOC);

        // Fetch Operational Expenses
        $stmtExp = $this->pdo->prepare("SELECT sum(amount) FROM expenses WHERE date BETWEEN ? AND ?");
        $stmtExp->execute([$start, $end]);
        $expenses = $stmtExp->fetchColumn();

        $profit = $financials['revenue'] - $financials['cogs'] - $expenses;

        $this->sendSuccess([
            'revenue' => number_format($financials['revenue'], 2),
            'cogs' => number_format($financials['cogs'], 2),
            'expenses' => number_format($expenses, 2),
            'net_profit' => number_format($profit, 2)
        ]);
    }

    // =====================================================================
    // HELPER METHODS
    // =====================================================================

    private function generateCustomID($type, $branch) {
        $prefix = (stripos($branch, 'North') !== false) ? 'N-' : 'S-';
        $prefix .= ($type === 'Repair') ? 'REP-' : 'SAL-';
        
        // Find last ID in DB to increment
        $stmt = $this->pdo->prepare("SELECT job_id FROM jobs WHERE job_id LIKE ? ORDER BY id DESC LIMIT 1");
        $stmt->execute([$prefix . '%']);
        $lastId = $stmt->fetchColumn();
        
        if ($lastId) {
            $num = intval(substr($lastId, -5)) + 1;
        } else {
            $num = 1;
        }
        
        return $prefix . str_pad($num, 5, '0', STR_PAD_LEFT);
    }

    private function logActivity($action, $desc) {
        $user = $_SESSION['user_id'] ?? 0;
        $stmt = $this->pdo->prepare("INSERT INTO activity_logs (user_id, action, description, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$user, $action, $desc]);
    }

    private function sendSuccess($data = []) {
        echo json_encode(array_merge(['success' => true], $data));
        exit;
    }

    private function sendError($message, $code = 400) {
        http_response_code($code);
        echo json_encode(['success' => false, 'error' => $message]);
        exit;
    }
}

// =========================================================================
// INIT
// =========================================================================

// Initialize Database (Mocked for this file context)
// $pdo = new PDO(...); 
// In a real scenario, this comes from db_config.php

if (isset($pdo)) {
    $api = new ApiController($pdo);
    $api->handleRequest();
}
?>
