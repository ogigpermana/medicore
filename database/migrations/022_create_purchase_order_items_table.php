<?php

return new class {
    public function up(PDO $pdo): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS purchase_order_items (
            id INT AUTO_INCREMENT PRIMARY KEY,
            purchase_order_id INT NOT NULL,
            product_id INT NOT NULL,
            quantity_ordered INT NOT NULL,
            quantity_received INT NOT NULL DEFAULT 0,
            unit_price DECIMAL(15,2) NOT NULL,
            discount_percent DECIMAL(5,2) DEFAULT 0.00,
            tax_percent DECIMAL(5,2) DEFAULT 0.00,
            subtotal DECIMAL(15,2) NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (purchase_order_id) REFERENCES purchase_orders(id) ON DELETE CASCADE,
            FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT,
            INDEX idx_poi_po (purchase_order_id),
            INDEX idx_poi_product (product_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

        $pdo->exec($sql);
    }

    public function down(PDO $pdo): void
    {
        $pdo->exec("DROP TABLE IF EXISTS purchase_order_items");
    }
};
