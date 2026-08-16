<?php

return new class {
    public function up(PDO $pdo): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS prescription_compound_items (
            id INT AUTO_INCREMENT PRIMARY KEY,
            compound_id INT NOT NULL,
            product_id INT NOT NULL,
            dose_per_pack VARCHAR(100) NULL,
            quantity_used INT NOT NULL DEFAULT 1,
            unit_price DECIMAL(15,2) NOT NULL DEFAULT 0.00,
            subtotal DECIMAL(15,2) NOT NULL DEFAULT 0.00,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (compound_id) REFERENCES prescription_compounds(id) ON DELETE CASCADE,
            FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT,
            INDEX idx_comp_item (compound_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

        $pdo->exec($sql);
    }

    public function down(PDO $pdo): void
    {
        $pdo->exec("DROP TABLE IF EXISTS prescription_compound_items");
    }
};
