<?php

return new class {
    public function up(PDO $pdo): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS ap_payments (
            id INT AUTO_INCREMENT PRIMARY KEY,
            goods_receipt_id INT NOT NULL,
            payment_date DATE NOT NULL,
            amount_paid DECIMAL(15,2) NOT NULL,
            payment_method ENUM('cash', 'bank_transfer', 'cheque', 'giro') DEFAULT 'bank_transfer',
            reference_number VARCHAR(100) NULL,
            notes TEXT NULL,
            created_by INT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (goods_receipt_id) REFERENCES goods_receipts(id) ON DELETE CASCADE,
            FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT,
            INDEX idx_app_gr (goods_receipt_id),
            INDEX idx_app_date (payment_date)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

        $pdo->exec($sql);
    }

    public function down(PDO $pdo): void
    {
        $pdo->exec("DROP TABLE IF EXISTS ap_payments");
    }
};
