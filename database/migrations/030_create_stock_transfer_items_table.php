<?php

return new class {
    public function up(PDO $pdo): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS stock_transfer_items (
            id INT AUTO_INCREMENT PRIMARY KEY,
            stock_transfer_id INT NOT NULL,
            product_id INT NOT NULL,
            batch_id INT NULL,
            qty_requested INT NOT NULL DEFAULT 1,
            qty_sent INT NOT NULL DEFAULT 0,
            qty_received INT NOT NULL DEFAULT 0,
            unit_buy_price DECIMAL(15,2) NOT NULL DEFAULT 0.00,
            notes TEXT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (stock_transfer_id) REFERENCES stock_transfers(id) ON DELETE CASCADE,
            FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT,
            FOREIGN KEY (batch_id) REFERENCES batches(id) ON DELETE SET NULL,
            INDEX idx_sti_transfer (stock_transfer_id),
            INDEX idx_sti_product (product_id),
            INDEX idx_sti_batch (batch_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

        $pdo->exec($sql);
    }

    public function down(PDO $pdo): void
    {
        $pdo->exec("DROP TABLE IF EXISTS stock_transfer_items");
    }
};
