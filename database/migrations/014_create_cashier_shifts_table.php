<?php

/**
 * Migration: Create Cashier Shifts Table
 * Description: Tracks cash drawer opening balance, shift duration, cash collected, and closing reconciliation
 */

return new class {
    public function up(PDO $pdo): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS cashier_shifts (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            opening_cash DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
            closing_cash DECIMAL(15, 2) NULL,
            expected_cash DECIMAL(15, 2) NULL,
            cash_difference DECIMAL(15, 2) NULL,
            total_sales_amount DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
            total_transactions INT NOT NULL DEFAULT 0,
            status ENUM('open', 'closed') NOT NULL DEFAULT 'open',
            opened_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            closed_at TIMESTAMP NULL,
            notes TEXT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            INDEX idx_user_status (user_id, status),
            INDEX idx_opened_at (opened_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        $pdo->exec($sql);
    }

    public function down(PDO $pdo): void
    {
        $pdo->exec("DROP TABLE IF EXISTS cashier_shifts");
    }
};
