<?php

/**
 * Migration: Create Sales Table
 * Description: Stores pharmacy sales transactions, customer info, tax, discounts, and payment methods
 */

return new class {
    public function up(PDO $pdo): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS sales (
            id INT AUTO_INCREMENT PRIMARY KEY,
            invoice_number VARCHAR(50) NOT NULL UNIQUE,
            cashier_shift_id INT NULL,
            user_id INT NOT NULL,
            customer_name VARCHAR(150) NULL DEFAULT 'General Patient (Walk-in)',
            customer_phone VARCHAR(50) NULL,
            subtotal DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
            discount_amount DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
            tax_amount DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
            total_amount DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
            payment_method ENUM('cash', 'qris', 'transfer', 'debit') NOT NULL DEFAULT 'cash',
            cash_tendered DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
            cash_change DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
            notes TEXT NULL,
            status ENUM('completed', 'refunded', 'cancelled') NOT NULL DEFAULT 'completed',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (cashier_shift_id) REFERENCES cashier_shifts(id) ON DELETE SET NULL,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            INDEX idx_invoice (invoice_number),
            INDEX idx_user (user_id),
            INDEX idx_created_at (created_at),
            INDEX idx_status (status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        $pdo->exec($sql);
    }

    public function down(PDO $pdo): void
    {
        $pdo->exec("DROP TABLE IF EXISTS sales");
    }
};
