<?php

/**
 * Migration: Create Products Table
 * Description: Stores pharmaceutical products and medication catalog
 */

return new class {
    public function up(PDO $pdo): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS products (
            id INT AUTO_INCREMENT PRIMARY KEY,
            sku VARCHAR(50) NOT NULL UNIQUE,
            barcode VARCHAR(100) NULL UNIQUE,
            name VARCHAR(200) NOT NULL,
            generic_name VARCHAR(200) NULL,
            category_id INT NULL,
            unit_id INT NULL,
            dosage VARCHAR(100) NULL,
            manufacturer VARCHAR(150) NULL,
            buy_price DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
            sell_price DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
            min_stock INT NOT NULL DEFAULT 10,
            stock_quantity INT NOT NULL DEFAULT 0,
            requires_prescription BOOLEAN DEFAULT FALSE,
            side_effects TEXT NULL,
            is_active BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
            FOREIGN KEY (unit_id) REFERENCES units(id) ON DELETE SET NULL,
            INDEX idx_sku (sku),
            INDEX idx_barcode (barcode),
            INDEX idx_name (name),
            INDEX idx_stock (stock_quantity),
            INDEX idx_is_active (is_active)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        $pdo->exec($sql);
    }

    public function down(PDO $pdo): void
    {
        $pdo->exec("DROP TABLE IF EXISTS products");
    }
};
