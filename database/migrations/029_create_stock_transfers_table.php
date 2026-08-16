<?php

return new class {
    public function up(PDO $pdo): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS stock_transfers (
            id INT AUTO_INCREMENT PRIMARY KEY,
            transfer_number VARCHAR(50) NOT NULL UNIQUE,
            source_branch_id INT NOT NULL,
            destination_branch_id INT NOT NULL,
            status ENUM('draft', 'pending_approval', 'in_transit', 'received', 'rejected', 'cancelled') NOT NULL DEFAULT 'draft',
            requested_by INT NOT NULL,
            approved_by INT NULL,
            received_by INT NULL,
            total_items INT DEFAULT 0,
            total_qty_sent INT DEFAULT 0,
            total_qty_received INT DEFAULT 0,
            driver_name VARCHAR(100) NULL,
            vehicle_number VARCHAR(50) NULL,
            shipping_notes TEXT NULL,
            departure_date DATETIME NULL,
            received_date DATETIME NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (source_branch_id) REFERENCES branches(id) ON DELETE RESTRICT,
            FOREIGN KEY (destination_branch_id) REFERENCES branches(id) ON DELETE RESTRICT,
            FOREIGN KEY (requested_by) REFERENCES users(id) ON DELETE RESTRICT,
            FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL,
            FOREIGN KEY (received_by) REFERENCES users(id) ON DELETE SET NULL,
            INDEX idx_st_number (transfer_number),
            INDEX idx_st_status (status),
            INDEX idx_st_source (source_branch_id),
            INDEX idx_st_dest (destination_branch_id),
            INDEX idx_st_created (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

        $pdo->exec($sql);
    }

    public function down(PDO $pdo): void
    {
        $pdo->exec("DROP TABLE IF EXISTS stock_transfers");
    }
};
