<?php

/**
 * Seeder: Seed Users
 * Description: Creates and updates test users for development with active verified status
 */

return new class {
    /**
     * Run the seeder
     */
    public function run($pdo): void
    {
        $users = [
            [
                'email' => 'admin@medicore.com',
                'password' => password_hash('admin123', PASSWORD_BCRYPT),
                'full_name' => 'Super Administrator',
                'phone' => '081234567890',
                'is_active' => 1,
                'email_verified' => 1,
                'email_verified_at' => date('Y-m-d H:i:s'),
                'role' => 'superadmin'
            ],
            [
                'email' => 'owner@medicore.com',
                'password' => password_hash('owner123', PASSWORD_BCRYPT),
                'full_name' => 'Pharmacy Owner',
                'phone' => '081234567891',
                'is_active' => 1,
                'email_verified' => 1,
                'email_verified_at' => date('Y-m-d H:i:s'),
                'role' => 'owner'
            ],
            [
                'email' => 'pharmacist@medicore.com',
                'password' => password_hash('pharmacist123', PASSWORD_BCRYPT),
                'full_name' => 'Dr. Sarah Wilson, S.Farm',
                'phone' => '081234567892',
                'is_active' => 1,
                'email_verified' => 1,
                'email_verified_at' => date('Y-m-d H:i:s'),
                'role' => 'pharmacist'
            ],
            [
                'email' => 'cashier@medicore.com',
                'password' => password_hash('cashier123', PASSWORD_BCRYPT),
                'full_name' => 'John Cashier',
                'phone' => '081234567893',
                'is_active' => 1,
                'email_verified' => 1,
                'email_verified_at' => date('Y-m-d H:i:s'),
                'role' => 'cashier'
            ],
            [
                'email' => 'warehouse@medicore.com',
                'password' => password_hash('warehouse123', PASSWORD_BCRYPT),
                'full_name' => 'Mike Warehouse',
                'phone' => '081234567894',
                'is_active' => 1,
                'email_verified' => 1,
                'email_verified_at' => date('Y-m-d H:i:s'),
                'role' => 'warehouse'
            ]
        ];

        foreach ($users as $user) {
            // Check if user exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$user['email']]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existing) {
                $update = $pdo->prepare("UPDATE users SET password = ?, full_name = ?, phone = ?, is_active = 1, email_verified = 1, failed_login_attempts = 0, locked_until = NULL WHERE id = ?");
                $update->execute([$user['password'], $user['full_name'], $user['phone'], $existing['id']]);
                $userId = $existing['id'];
                echo "User updated: {$user['email']} (ID: {$userId})\n";
            } else {
                $insert = $pdo->prepare("INSERT INTO users (email, password, full_name, phone, is_active, email_verified, email_verified_at) VALUES (?, ?, ?, ?, 1, 1, NOW())");
                $insert->execute([$user['email'], $user['password'], $user['full_name'], $user['phone']]);
                $userId = $pdo->lastInsertId();
                echo "User created: {$user['email']} (ID: {$userId})\n";
            }

            // Assign role
            $roleStmt = $pdo->prepare("SELECT id FROM roles WHERE name = ?");
            $roleStmt->execute([$user['role']]);
            $role = $roleStmt->fetch(PDO::FETCH_ASSOC);

            if ($role) {
                // Remove existing roles then insert
                $pdo->prepare("DELETE FROM user_roles WHERE user_id = ?")->execute([$userId]);
                $urStmt = $pdo->prepare("INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)");
                $urStmt->execute([$userId, $role['id']]);
                echo "Role assigned: {$user['email']} -> {$user['role']}\n";
            }
        }
    }
};