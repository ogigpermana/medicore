<?php

/**
 * Migration: Create Audit Logs Table
 * Description: Creates the audit_logs table for security and compliance
 */

return new class {
    /**
     * Run the migration
     */
    public function up($pdo): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS audit_logs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NULL,
            action VARCHAR(100) NOT NULL,
            entity_type VARCHAR(50) NOT NULL,
            entity_id INT NULL,
            description TEXT,
            ip_address VARCHAR(45),
            user_agent TEXT,
            metadata JSON,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_user_id (user_id),
            INDEX idx_action (action),
            INDEX idx_entity (entity_type, entity_id),
            INDEX idx_created_at (created_at),
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        $pdo->exec($sql);
        echo "Audit logs table created successfully.\n";
    }

    /**
     * Reverse the migration
     */
    public function down($pdo): void
    {
        $sql = "DROP TABLE IF EXISTS audit_logs";
        $pdo->exec($sql);
        echo "Audit logs table dropped successfully.\n";
    }
};