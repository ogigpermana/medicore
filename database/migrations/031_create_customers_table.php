<?php

/**
 * Migration: Create Customers (Patients & CRM) Table
 * Description: Stores customer and patient master records, clinical allergy flags, chronic illness history, and loyalty spend metrics.
 */

return new class {
    public function up(PDO $pdo): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS customers (
            id INT AUTO_INCREMENT PRIMARY KEY,
            code VARCHAR(50) NOT NULL UNIQUE,
            name VARCHAR(150) NOT NULL,
            phone VARCHAR(50) NULL,
            email VARCHAR(100) NULL,
            gender ENUM('male', 'female', 'other') NULL DEFAULT 'other',
            birth_date DATE NULL,
            address TEXT NULL,
            allergy_notes TEXT NULL,
            chronic_disease_notes TEXT NULL,
            total_spend DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
            total_visits INT NOT NULL DEFAULT 0,
            last_visit_at DATETIME NULL,
            is_active BOOLEAN NOT NULL DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_code (code),
            INDEX idx_phone (phone),
            INDEX idx_name (name),
            INDEX idx_is_active (is_active)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        $pdo->exec($sql);
    }

    public function down(PDO $pdo): void
    {
        $pdo->exec("DROP TABLE IF EXISTS customers");
    }
};
