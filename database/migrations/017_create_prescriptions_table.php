<?php

return new class {
    public function up(PDO $pdo): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS prescriptions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            prescription_number VARCHAR(50) NOT NULL UNIQUE,
            patient_name VARCHAR(150) NOT NULL,
            patient_age INT NULL,
            patient_gender ENUM('male', 'female', 'other') DEFAULT 'male',
            patient_weight DECIMAL(5,2) NULL,
            doctor_name VARCHAR(150) NOT NULL,
            doctor_sip VARCHAR(100) NULL,
            doctor_clinic VARCHAR(150) NULL,
            diagnosis TEXT NULL,
            clinical_notes TEXT NULL,
            status ENUM('pending', 'reviewed', 'compounding', 'ready', 'dispensed', 'cancelled') DEFAULT 'pending',
            pharmacist_id INT NULL,
            pharmacist_notes TEXT NULL,
            total_amount DECIMAL(15,2) DEFAULT 0.00,
            tuslah_fee DECIMAL(15,2) DEFAULT 0.00,
            embalase_fee DECIMAL(15,2) DEFAULT 0.00,
            sale_id INT NULL,
            reviewed_at DATETIME NULL,
            dispensed_at DATETIME NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (pharmacist_id) REFERENCES users(id) ON DELETE SET NULL,
            FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE SET NULL,
            INDEX idx_rx_status (status),
            INDEX idx_rx_patient (patient_name),
            INDEX idx_rx_created (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

        $pdo->exec($sql);
    }

    public function down(PDO $pdo): void
    {
        $pdo->exec("DROP TABLE IF EXISTS prescriptions");
    }
};
