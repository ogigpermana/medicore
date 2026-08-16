<?php

/**
 * Migration: Create Stock Movements Table
 * Description: Immutable ledger of all inventory transactions (in, out, adjustment, POS sales, returns)
 */

return new class {
    public function up(PDO $pdo): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS stock_movements (
            id INT AUTO_INCREMENT PRIMARY KEY,
            product_id INT NOT NULL,
            batch_id INT NULL,
            type ENUM('in', 'out', 'adjust_plus', 'adjust_minus', 'sale', 'return', 'expired') NOT NULL,
            quantity INT NOT NULL,
            balance_after INT NOT NULL,
            reference_type VARCHAR(50) NULL,
            reference_id INT NULL,
            user_id INT NULL,
            notes TEXT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
            FOREIGN KEY (batch_id) REFERENCES batches(id) ON DELETE SET NULL,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
            INDEX idx_product_movement (product_id, created_at),
            INDEX idx_type (type),
            INDEX idx_created (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        $pdo->exec($sql);
    }

    public function down(PDO $pdo): void
    {
        $pdo->exec("DROP TABLE IF EXISTS stock_movements");
    }
};
