<?php

return new class {
    public function up(PDO $pdo): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS goods_receipts (
            id INT AUTO_INCREMENT PRIMARY KEY,
            grn_number VARCHAR(50) NOT NULL UNIQUE,
            purchase_order_id INT NULL,
            supplier_id INT NOT NULL,
            received_by INT NOT NULL,
            invoice_number VARCHAR(100) NOT NULL,
            invoice_date DATE NOT NULL,
            due_date DATE NOT NULL,
            subtotal DECIMAL(15,2) DEFAULT 0.00,
            tax_amount DECIMAL(15,2) DEFAULT 0.00,
            total_amount DECIMAL(15,2) NOT NULL,
            amount_paid DECIMAL(15,2) DEFAULT 0.00,
            payment_status ENUM('unpaid', 'partial', 'paid') DEFAULT 'unpaid',
            notes TEXT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (purchase_order_id) REFERENCES purchase_orders(id) ON DELETE SET NULL,
            FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE RESTRICT,
            FOREIGN KEY (received_by) REFERENCES users(id) ON DELETE RESTRICT,
            INDEX idx_grn_po (purchase_order_id),
            INDEX idx_grn_supplier (supplier_id),
            INDEX idx_grn_invoice (invoice_number),
            INDEX idx_grn_payment_status (payment_status),
            INDEX idx_grn_due_date (due_date)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

        $pdo->exec($sql);
    }

    public function down(PDO $pdo): void
    {
        $pdo->exec("DROP TABLE IF EXISTS goods_receipts");
    }
};
