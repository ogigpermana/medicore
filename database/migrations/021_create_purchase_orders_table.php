<?php

return new class {
    public function up(PDO $pdo): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS purchase_orders (
            id INT AUTO_INCREMENT PRIMARY KEY,
            po_number VARCHAR(50) NOT NULL UNIQUE,
            sp_type ENUM('regular', 'precursor', 'oot', 'narcotic_psychotropic') DEFAULT 'regular',
            supplier_id INT NOT NULL,
            user_id INT NOT NULL,
            order_date DATE NOT NULL,
            expected_delivery_date DATE NULL,
            status ENUM('draft', 'ordered', 'partial_received', 'received', 'cancelled') DEFAULT 'ordered',
            payment_terms ENUM('cod', 'net_7', 'net_14', 'net_30', 'net_60') DEFAULT 'net_30',
            subtotal DECIMAL(15,2) DEFAULT 0.00,
            discount_amount DECIMAL(15,2) DEFAULT 0.00,
            tax_amount DECIMAL(15,2) DEFAULT 0.00,
            grand_total DECIMAL(15,2) DEFAULT 0.00,
            notes TEXT NULL,
            pharmacist_sipa VARCHAR(100) NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE RESTRICT,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT,
            INDEX idx_po_status (status),
            INDEX idx_po_supplier (supplier_id),
            INDEX idx_po_date (order_date)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

        $pdo->exec($sql);
    }

    public function down(PDO $pdo): void
    {
        $pdo->exec("DROP TABLE IF EXISTS purchase_orders");
    }
};
