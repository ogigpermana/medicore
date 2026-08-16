<?php

/**
 * Migration: Create User Roles Table
 * Description: Creates the user_roles table for role assignments
 */

return new class {
    /**
     * Run the migration
     */
    public function up($pdo): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS user_roles (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            role_id INT NOT NULL,
            assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            assigned_by INT NULL,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
            FOREIGN KEY (assigned_by) REFERENCES users(id) ON DELETE SET NULL,
            UNIQUE KEY unique_user_role (user_id, role_id),
            INDEX idx_user_id (user_id),
            INDEX idx_role_id (role_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        $pdo->exec($sql);
        echo "User roles table created successfully.\n";
    }

    /**
     * Reverse the migration
     */
    public function down($pdo): void
    {
        $sql = "DROP TABLE IF EXISTS user_roles";
        $pdo->exec($sql);
        echo "User roles table dropped successfully.\n";
    }
};