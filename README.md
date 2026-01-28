# Enterprise Repair Shop ERP - Backend API

## Overview
This is the core backend API for a custom ERP solution designed for a high-volume electronics repair franchise. It handles inventory management, point-of-sale (POS) transactions, automated stock deduction based on repair descriptions, and technician workflow tracking.

## Technical Highlights
- **Architecture:** Monolithic PHP API refactored into a service-based controller pattern.
- **Security:** Implements custom Rate Limiting, User-Agent analysis (Anti-Bot), and CSRF protection.
- **Complex Inventory Logic:** Features a "Fuzzy Match" algorithm that parses free-text repair descriptions (e.g., "iPhone 11 LCD (x2)") to automatically deduct stock from specific branch inventories.
- **Database:** Optimized MySQL/MariaDB queries with heavy use of indexing and transaction management (ACID compliance) for stock transfers.

## Key Features
- **Dynamic Stock Management:** Automated deduction/restoration of stock upon job creation or cancellation.
- **Role-Based Access Control (RBAC):** Granular permissions for admins, branch managers, and technicians.
- **Financial Reporting:** Real-time P&L generation with calculating logic for parts cost vs. service labor.
- **Hardware Integration:** Generates ESC/POS commands for thermal receipt printers directly from the backend.

## Stack
- PHP 8.x
- MySQL (PDO with Prepared Statements)
- JSON REST API
