<?php

return new class {
    public function up(PDO $pdo): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS goods_receipt_items (
            id INT AUTO_INCREMENT PRIMARY KEY,
            goods_receipt_id INT NOT NULL,
            product_id INT NOT NULL,
            batch_number VARCHAR(50) NOT NULL,
            expiry_date DATE NOT NULL,
            quantity_received INT NOT NULL,
            buy_price DECIMAL(15,2) NOT NULL,
            subtotal DECIMAL(15,2) NOT NULL,
            batch_id INT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (goods_receipt_id) REFERENCES goods_receipts(id) ON DELETE CASCADE,
            FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT,
            FOREIGN KEY (batch_id) REFERENCES batches(id) ON DELETE SET NULL,
            INDEX idx_gri_gr (goods_receipt_id),
            INDEX idx_gri_batch (batch_number),
            INDEX idx_gri_expiry (expiry_date)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

        $pdo->exec($sql);
    }

    public function down(PDO $pdo): void
    {
        $pdo->exec("DROP TABLE IF EXISTS goods_receipt_items");
    }
};
