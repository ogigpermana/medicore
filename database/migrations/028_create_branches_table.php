<?php

return new class {
    public function up(PDO $pdo): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS branches (
            id INT AUTO_INCREMENT PRIMARY KEY,
            code VARCHAR(20) NOT NULL UNIQUE,
            name VARCHAR(150) NOT NULL,
            type ENUM('main_branch', 'sub_branch', 'warehouse') NOT NULL DEFAULT 'sub_branch',
            phone VARCHAR(50) NULL,
            email VARCHAR(100) NULL,
            address TEXT NOT NULL,
            pharmacist_in_charge VARCHAR(150) NULL,
            sipa_number VARCHAR(100) NULL,
            sia_number VARCHAR(100) NULL,
            is_active BOOLEAN DEFAULT TRUE,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_branch_code (code),
            INDEX idx_branch_type (type),
            INDEX idx_branch_active (is_active)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

        $pdo->exec($sql);
    }

    public function down(PDO $pdo): void
    {
        $pdo->exec("DROP TABLE IF EXISTS branches");
    }
};
