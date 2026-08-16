<?php

/**
 * Seeder: Seed Initial Customers & Patient CRM Profiles
 */

require_once __DIR__ . '/../../vendor/autoload.php';

$app = \Core\Application::create();
$db = $app->getContainer()->get(\Core\Database::class);

$customers = [
    [
        'code' => 'CUST-0001',
        'name' => 'Ibu Siti Aminah',
        'phone' => '081234567890',
        'email' => 'siti.aminah@gmail.com',
        'gender' => 'female',
        'birth_date' => '1975-06-12',
        'address' => 'Jl. Kebon Sirih No. 45, Jakarta Pusat',
        'allergy_notes' => 'Alergi Golongan Penisilin (Amoxicillin, Ampicillin) - Ruam & Gatal Akut',
        'chronic_disease_notes' => 'Hipertensi Primer Stadium 1, Rutin Amlodipine 5mg',
        'total_spend' => 485000.00,
        'total_visits' => 4,
        'last_visit_at' => date('Y-m-d H:i:s', strtotime('-3 days')),
        'is_active' => 1
    ],
    [
        'code' => 'CUST-0002',
        'name' => 'Bpk. Hendra Gunawan',
        'phone' => '081398765432',
        'email' => 'hendra.gunawan@yahoo.com',
        'gender' => 'male',
        'birth_date' => '1968-11-20',
        'address' => 'Jl. Tebet Barat Dalam No. 12, Jakarta Selatan',
        'allergy_notes' => 'Alergi Obat Golongan Sulfonamida (Cotrimoxazole)',
        'chronic_disease_notes' => 'Diabetes Mellitus Tipe 2, Terapi Metformin 500mg',
        'total_spend' => 920000.00,
        'total_visits' => 6,
        'last_visit_at' => date('Y-m-d H:i:s', strtotime('-1 days')),
        'is_active' => 1
    ],
    [
        'code' => 'CUST-0003',
        'name' => 'Bpk. Budi Santoso',
        'phone' => '081122334455',
        'email' => 'budi.santoso@corporate.co.id',
        'gender' => 'male',
        'birth_date' => '1988-04-05',
        'address' => 'Jl. Menteng Raya No. 8, Jakarta Pusat',
        'allergy_notes' => null,
        'chronic_disease_notes' => null,
        'total_spend' => 245000.00,
        'total_visits' => 2,
        'last_visit_at' => date('Y-m-d H:i:s', strtotime('-7 days')),
        'is_active' => 1
    ],
    [
        'code' => 'CUST-0004',
        'name' => 'Ny. Dewi Lestari',
        'phone' => '081877665544',
        'email' => 'dewi.lestari@gmail.com',
        'gender' => 'female',
        'birth_date' => '1982-09-18',
        'address' => 'Jl. Kemang Timur No. 24, Jakarta Selatan',
        'allergy_notes' => 'Alergi NSAID (Aspirin, Asam Mefenamat) - Memicu Bronkospasme',
        'chronic_disease_notes' => 'Asma Bronkial Intermiten, Pemakai Inhaler Salbutamol',
        'total_spend' => 650000.00,
        'total_visits' => 3,
        'last_visit_at' => date('Y-m-d H:i:s', strtotime('-12 days')),
        'is_active' => 1
    ],
    [
        'code' => 'CUST-0005',
        'name' => 'An. Kevin Pratama',
        'phone' => '081299887766',
        'email' => 'ortu.kevin@gmail.com',
        'gender' => 'male',
        'birth_date' => '2019-02-14',
        'address' => 'Jl. Cempaka Putih Tengah No. 5, Jakarta Pusat',
        'allergy_notes' => null,
        'chronic_disease_notes' => 'Pasien Pediatrik (Anak 5 Tahun)',
        'total_spend' => 175000.00,
        'total_visits' => 2,
        'last_visit_at' => date('Y-m-d H:i:s', strtotime('-15 days')),
        'is_active' => 1
    ]
];

foreach ($customers as $c) {
    $exists = $db->fetch("SELECT id FROM customers WHERE code = ?", [$c['code']]);
    if (!$exists) {
        $db->insert('customers', $c);
        echo "Seeded customer: {$c['code']} - {$c['name']}\n";
    }
}

echo "Customers seeding completed successfully.\n";
