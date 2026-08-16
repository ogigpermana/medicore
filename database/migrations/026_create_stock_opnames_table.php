<?php

return new class {
    public function up(PDO $pdo): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS stock_opnames (
            id INT AUTO_INCREMENT PRIMARY KEY,
            opname_number VARCHAR(50) NOT NULL UNIQUE,
            title VARCHAR(200) NOT NULL,
            status ENUM('draft', 'in_progress', 'completed', 'cancelled') DEFAULT 'draft',
            user_id INT NOT NULL,
            approved_by INT NULL,
            total_items_counted INT DEFAULT 0,
            total_system_qty INT DEFAULT 0,
            total_physical_qty INT DEFAULT 0,
            total_variance_qty INT DEFAULT 0,
            total_variance_value DECIMAL(15,2) DEFAULT 0.00,
            notes TEXT NULL,
            completed_at DATETIME NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT,
            FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL,
            INDEX idx_so_status (status),
            INDEX idx_so_date (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

        $pdo->exec($sql);
    }

    public function down(PDO $pdo): void
    {
        $pdo->exec("DROP TABLE IF EXISTS stock_opnames");
    }
};
