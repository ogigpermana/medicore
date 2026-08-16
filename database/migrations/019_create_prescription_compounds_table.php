<?php

return new class {
    public function up(PDO $pdo): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS prescription_compounds (
            id INT AUTO_INCREMENT PRIMARY KEY,
            prescription_id INT NOT NULL,
            compound_name VARCHAR(150) NOT NULL,
            packaging_type ENUM('puyer', 'capsule', 'syrup_mixture', 'ointment') DEFAULT 'puyer',
            quantity_pack INT NOT NULL DEFAULT 10,
            dosage_instructions VARCHAR(255) NOT NULL,
            compounding_fee DECIMAL(15,2) DEFAULT 5000.00,
            packaging_fee DECIMAL(15,2) DEFAULT 2000.00,
            total_price DECIMAL(15,2) NOT NULL DEFAULT 0.00,
            notes TEXT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (prescription_id) REFERENCES prescriptions(id) ON DELETE CASCADE,
            INDEX idx_compound_rx (prescription_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

        $pdo->exec($sql);
    }

    public function down(PDO $pdo): void
    {
        $pdo->exec("DROP TABLE IF EXISTS prescription_compounds");
    }
};
