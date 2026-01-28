-- Enterprise ERP Database Schema
-- Version: 2.1.0
-- Engine: InnoDB
-- Charset: utf8mb4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+08:00";

-- 1. Users & Permissions (RBAC)
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','staff','technician') DEFAULT 'staff',
  `assigned_branch` varchar(50) DEFAULT NULL,
  `can_edit` tinyint(1) DEFAULT 0,
  `can_delete` tinyint(1) DEFAULT 0,
  `last_login` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Inventory / Stock List
CREATE TABLE `stock_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `part_name` varchar(255) NOT NULL,
  `type` varchar(50) DEFAULT 'Part',
  `category` varchar(100) DEFAULT 'General',
  `stock_gombak` int(11) DEFAULT 0,
  `stock_sr` int(11) DEFAULT 0,
  `cost` decimal(10,2) DEFAULT 0.00,
  `supplier` varchar(100) DEFAULT NULL,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_part_name` (`part_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Jobs / Repair Tickets
CREATE TABLE `jobs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `job_id` varchar(20) NOT NULL, -- Custom ID like REP-2024-001
  `branch` varchar(50) NOT NULL,
  `customer` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `device_model` varchar(100) DEFAULT NULL,
  `repair_desc` text, -- Stores "Screen (x1) || Battery (x1)"
  `price` decimal(10,2) NOT NULL,
  `status` enum('Pending','Completed','Cancelled','Return') DEFAULT 'Pending',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `completed_at` datetime DEFAULT NULL,
  `is_deleted` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `job_id` (`job_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Activity Logs (Audit Trail)
CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `action` varchar(50) NOT NULL,
  `description` text,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

COMMIT;
