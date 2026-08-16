<?php

/**
 * Migration: Create password_resets table
 * Date: 2026-08-16
 */

return new class {
    /**
     * Run the migration
     */
    public function up($pdo): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS password_resets (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) NOT NULL,
            token VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            expires_at DATETIME NOT NULL,
            INDEX idx_email (email),
            INDEX idx_token (token),
            INDEX idx_expires_at (expires_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        $pdo->exec($sql);
        echo "Password resets table created successfully.\n";
    }

    /**
     * Reverse the migration
     */
    public function down($pdo): void
    {
        $sql = "DROP TABLE IF EXISTS password_resets";
        $pdo->exec($sql);
        echo "Password resets table dropped successfully.\n";
    }
};