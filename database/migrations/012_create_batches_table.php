<?php

/**
 * Migration: Create Batches Table
 * Description: Stores batch lots, manufacturing & expiry dates for automated FEFO management
 */

return new class {
    public function up(PDO $pdo): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS batches (
            id INT AUTO_INCREMENT PRIMARY KEY,
            product_id INT NOT NULL,
            batch_number VARCHAR(100) NOT NULL,
            expiry_date DATE NOT NULL,
            manufacture_date DATE NULL,
            initial_quantity INT NOT NULL,
            current_quantity INT NOT NULL,
            buy_price DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
            supplier_id INT NULL,
            received_date DATE NOT NULL,
            is_active BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
            FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE SET NULL,
            INDEX idx_product_batch (product_id, batch_number),
            INDEX idx_expiry (expiry_date),
            INDEX idx_current_qty (current_quantity)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        $pdo->exec($sql);
    }

    public function down(PDO $pdo): void
    {
        $pdo->exec("DROP TABLE IF EXISTS batches");
    }
};
