<?php

/**
 * Migration: Create Sale Items Table
 * Description: Stores line items for each transaction, linked to specific batch lots for FEFO auditing
 */

return new class {
    public function up(PDO $pdo): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS sale_items (
            id INT AUTO_INCREMENT PRIMARY KEY,
            sale_id INT NOT NULL,
            product_id INT NOT NULL,
            batch_id INT NULL,
            quantity INT NOT NULL DEFAULT 1,
            unit_price DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
            subtotal DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
            discount_amount DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
            total_price DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE CASCADE,
            FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
            FOREIGN KEY (batch_id) REFERENCES batches(id) ON DELETE SET NULL,
            INDEX idx_sale (sale_id),
            INDEX idx_product (product_id),
            INDEX idx_batch (batch_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        $pdo->exec($sql);
    }

    public function down(PDO $pdo): void
    {
        $pdo->exec("DROP TABLE IF EXISTS sale_items");
    }
};
