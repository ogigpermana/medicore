<?php

return new class {
    public function up(PDO $pdo): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS stock_opname_items (
            id INT AUTO_INCREMENT PRIMARY KEY,
            stock_opname_id INT NOT NULL,
            product_id INT NOT NULL,
            batch_id INT NULL,
            system_qty INT NOT NULL DEFAULT 0,
            physical_qty INT NOT NULL DEFAULT 0,
            variance_qty INT NOT NULL DEFAULT 0,
            buy_price DECIMAL(15,2) NOT NULL DEFAULT 0.00,
            variance_value DECIMAL(15,2) NOT NULL DEFAULT 0.00,
            adjustment_reason ENUM('matched', 'damaged', 'expired', 'lost', 'count_error', 'bonus_sample') DEFAULT 'matched',
            notes TEXT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (stock_opname_id) REFERENCES stock_opnames(id) ON DELETE CASCADE,
            FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT,
            FOREIGN KEY (batch_id) REFERENCES batches(id) ON DELETE SET NULL,
            INDEX idx_soi_opname (stock_opname_id),
            INDEX idx_soi_product (product_id),
            INDEX idx_soi_batch (batch_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

        $pdo->exec($sql);
    }

    public function down(PDO $pdo): void
    {
        $pdo->exec("DROP TABLE IF EXISTS stock_opname_items");
    }
};
