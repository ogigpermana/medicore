<?php

/**
 * Migration: Add email verification columns to users table
 * Date: 2026-08-16
 */

return new class {
    /**
     * Run the migration
     */
    public function up($pdo): void
    {
        $sql = "ALTER TABLE users 
                ADD COLUMN email_verification_token VARCHAR(255) NULL AFTER email_verified_at,
                ADD COLUMN email_verification_expires DATETIME NULL AFTER email_verification_token";

        $pdo->exec($sql);
        echo "Email verification columns added successfully.\n";
    }

    /**
     * Reverse the migration
     */
    public function down($pdo): void
    {
        $sql = "ALTER TABLE users 
                DROP COLUMN email_verification_expires,
                DROP COLUMN email_verification_token";
        
        $pdo->exec($sql);
        echo "Email verification columns dropped successfully.\n";
    }
};