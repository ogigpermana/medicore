<?php

/**
 * Migration: Create Roles Table
 * Description: Creates the roles table for RBAC
 */

return new class {
    /**
     * Run the migration
     */
    public function up($pdo): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS roles (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(50) UNIQUE NOT NULL,
            display_name VARCHAR(100) NOT NULL,
            description TEXT,
            permissions JSON,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_name (name)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        $pdo->exec($sql);
        echo "Roles table created successfully.\n";

        // Insert default roles
        $this->insertDefaultRoles($pdo);
    }

    /**
     * Insert default roles
     */
    private function insertDefaultRoles($pdo): void
    {
        $roles = [
            [
                'name' => 'superadmin',
                'display_name' => 'Super Administrator',
                'description' => 'Full system access',
                'permissions' => json_encode(['*'])
            ],
            [
                'name' => 'owner',
                'display_name' => 'Pharmacy Owner',
                'description' => 'Full business access',
                'permissions' => json_encode([
                    'products.*',
                    'sales.*',
                    'reports.*',
                    'customers.*',
                    'prescriptions.*',
                    'users.read'
                ])
            ],
            [
                'name' => 'pharmacist',
                'display_name' => 'Pharmacist',
                'description' => 'Pharmacy staff with prescription management',
                'permissions' => json_encode([
                    'products.read',
                    'products.write',
                    'prescriptions.*',
                    'customers.read',
                    'sales.read'
                ])
            ],
            [
                'name' => 'cashier',
                'display_name' => 'Cashier',
                'description' => 'Point of sale staff',
                'permissions' => json_encode([
                    'sales.*',
                    'products.read',
                    'customers.read'
                ])
            ],
            [
                'name' => 'warehouse',
                'display_name' => 'Warehouse Staff',
                'description' => 'Inventory management',
                'permissions' => json_encode([
                    'products.*',
                    'stock.*',
                    'suppliers.*'
                ])
            ]
        ];

        $stmt = $pdo->prepare("INSERT INTO roles (name, display_name, description, permissions) VALUES (?, ?, ?, ?)");
        
        foreach ($roles as $role) {
            $stmt->execute([
                $role['name'],
                $role['display_name'],
                $role['description'],
                $role['permissions']
            ]);
        }
        
        echo "Default roles inserted successfully.\n";
    }

    /**
     * Reverse the migration
     */
    public function down($pdo): void
    {
        $sql = "DROP TABLE IF EXISTS roles";
        $pdo->exec($sql);
        echo "Roles table dropped successfully.\n";
    }
};