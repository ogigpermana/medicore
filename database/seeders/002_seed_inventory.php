<?php

/**
 * Seeder: Seed Inventory Master Data
 * Description: Populates realistic pharmaceutical categories, units, suppliers, products, and FEFO batches
 */

return new class {
    public function run(PDO $pdo): void
    {
        // 1. Seed Categories
        $categories = [
            ['name' => 'Antibiotics', 'slug' => 'antibiotics', 'description' => 'Prescription antibacterial medications', 'requires_prescription' => 1],
            ['name' => 'Analgesics & Antipyretics', 'slug' => 'analgesics', 'description' => 'Pain relief and fever reduction', 'requires_prescription' => 0],
            ['name' => 'Gastrointestinal', 'slug' => 'gastrointestinal', 'description' => 'Antacids, proton-pump inhibitors, and digestive aids', 'requires_prescription' => 0],
            ['name' => 'Cardiovascular', 'slug' => 'cardiovascular', 'description' => 'Hypertension, heart, and lipid-lowering drugs', 'requires_prescription' => 1],
            ['name' => 'Antihistamines & Allergy', 'slug' => 'antihistamines', 'description' => 'Allergy and symptom relief', 'requires_prescription' => 0],
            ['name' => 'Vitamins & Supplements', 'slug' => 'vitamins', 'description' => 'Immune boosters and nutritional supplements', 'requires_prescription' => 0]
        ];

        $catStmt = $pdo->prepare("INSERT INTO categories (name, slug, description, requires_prescription) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE name = VALUES(name), description = VALUES(description)");
        foreach ($categories as $cat) {
            $catStmt->execute([$cat['name'], $cat['slug'], $cat['description'], $cat['requires_prescription']]);
        }
        echo "Categories seeded successfully.\n";

        // 2. Seed Units
        $units = [
            ['name' => 'Strip', 'symbol' => 'str'],
            ['name' => 'Tablet', 'symbol' => 'tab'],
            ['name' => 'Capsule', 'symbol' => 'cap'],
            ['name' => 'Bottle', 'symbol' => 'btl'],
            ['name' => 'Box', 'symbol' => 'box'],
            ['name' => 'Tube', 'symbol' => 'tube'],
            ['name' => 'Vial / Ampoule', 'symbol' => 'vial']
        ];

        $unitStmt = $pdo->prepare("INSERT INTO units (name, symbol) VALUES (?, ?) ON DUPLICATE KEY UPDATE symbol = VALUES(symbol)");
        foreach ($units as $u) {
            $unitStmt->execute([$u['name'], $u['symbol']]);
        }
        echo "Units seeded successfully.\n";

        // 3. Seed Suppliers
        $suppliers = [
            [
                'code' => 'SUP-KF-001',
                'name' => 'PT Kimia Farma Trading & Distribution',
                'contact_person' => 'Bambang Sugiarto',
                'phone' => '021-3847712',
                'email' => 'order@kimiafarma-dist.co.id',
                'address' => 'Jl. Veteran No. 9, Jakarta Pusat'
            ],
            [
                'code' => 'SUP-ENS-002',
                'name' => 'PT Enseval Putera Megatrading Tbk',
                'contact_person' => 'Dewi Lestari',
                'phone' => '021-4609042',
                'email' => 'sales@enseval.com',
                'address' => 'Kawasan Industri Pulogadung, Jakarta Timur'
            ],
            [
                'code' => 'SUP-AAM-003',
                'name' => 'PT Anugrah Argon Medica',
                'contact_person' => 'Hendra Wijaya',
                'phone' => '021-8984211',
                'email' => 'cs@anugrah-argon.com',
                'address' => 'Titan Center Lt. 6, Bintaro Jaya Sektor 7, Tangerang Selatan'
            ]
        ];

        $supStmt = $pdo->prepare("INSERT INTO suppliers (code, name, contact_person, phone, email, address) VALUES (?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE name = VALUES(name), phone = VALUES(phone)");
        foreach ($suppliers as $sup) {
            $supStmt->execute([$sup['code'], $sup['name'], $sup['contact_person'], $sup['phone'], $sup['email'], $sup['address']]);
        }
        echo "Suppliers seeded successfully.\n";

        // Get Category IDs
        $catMap = $pdo->query("SELECT slug, id FROM categories")->fetchAll(PDO::FETCH_KEY_PAIR);
        $unitMap = $pdo->query("SELECT symbol, id FROM units")->fetchAll(PDO::FETCH_KEY_PAIR);
        $supMap = $pdo->query("SELECT code, id FROM suppliers")->fetchAll(PDO::FETCH_KEY_PAIR);

        // 4. Seed Products
        $products = [
            [
                'sku' => 'MED-AMX-500',
                'barcode' => '8991001234511',
                'name' => 'Amoxicillin 500mg',
                'generic_name' => 'Amoxicillin Trihydrate',
                'category_id' => $catMap['antibiotics'] ?? 1,
                'unit_id' => $unitMap['str'] ?? 1,
                'dosage' => '500 mg',
                'manufacturer' => 'Kimia Farma',
                'buy_price' => 14000.00,
                'sell_price' => 18500.00,
                'min_stock' => 20,
                'stock_quantity' => 125,
                'requires_prescription' => 1
            ],
            [
                'sku' => 'MED-PCT-650',
                'barcode' => '8991001234528',
                'name' => 'Paracetamol Forte 650mg',
                'generic_name' => 'Paracetamol',
                'category_id' => $catMap['analgesics'] ?? 2,
                'unit_id' => $unitMap['str'] ?? 1,
                'dosage' => '650 mg',
                'manufacturer' => 'Kalbe Farma',
                'buy_price' => 8500.00,
                'sell_price' => 12000.00,
                'min_stock' => 30,
                'stock_quantity' => 210,
                'requires_prescription' => 0
            ],
            [
                'sku' => 'MED-OMP-020',
                'barcode' => '8991001234535',
                'name' => 'Omeprazole 20mg Capsule',
                'generic_name' => 'Omeprazole Sodium',
                'category_id' => $catMap['gastrointestinal'] ?? 3,
                'unit_id' => $unitMap['str'] ?? 1,
                'dosage' => '20 mg',
                'manufacturer' => 'Dexa Medica',
                'buy_price' => 32000.00,
                'sell_price' => 45000.00,
                'min_stock' => 15,
                'stock_quantity' => 80,
                'requires_prescription' => 0
            ],
            [
                'sku' => 'MED-CTZ-010',
                'barcode' => '8991001234542',
                'name' => 'Cetirizine 10mg Film Tablet',
                'generic_name' => 'Cetirizine Dihydrochloride',
                'category_id' => $catMap['antihistamines'] ?? 5,
                'unit_id' => $unitMap['str'] ?? 1,
                'dosage' => '10 mg',
                'manufacturer' => 'Sanbe Farma',
                'buy_price' => 10500.00,
                'sell_price' => 15000.00,
                'min_stock' => 25,
                'stock_quantity' => 150,
                'requires_prescription' => 0
            ],
            [
                'sku' => 'MED-AML-005',
                'barcode' => '8991001234559',
                'name' => 'Amlodipine 5mg',
                'generic_name' => 'Amlodipine Besylate',
                'category_id' => $catMap['cardiovascular'] ?? 4,
                'unit_id' => $unitMap['str'] ?? 1,
                'dosage' => '5 mg',
                'manufacturer' => 'Phapros',
                'buy_price' => 18000.00,
                'sell_price' => 24000.00,
                'min_stock' => 20,
                'stock_quantity' => 95,
                'requires_prescription' => 1
            ],
            [
                'sku' => 'MED-VTC-500',
                'barcode' => '8991001234566',
                'name' => 'Vitamin C 500mg Buffered',
                'generic_name' => 'Ascorbic Acid',
                'category_id' => $catMap['vitamins'] ?? 6,
                'unit_id' => $unitMap['btl'] ?? 4,
                'dosage' => '500 mg',
                'manufacturer' => 'Darya-Varia',
                'buy_price' => 38000.00,
                'sell_price' => 52000.00,
                'min_stock' => 10,
                'stock_quantity' => 45,
                'requires_prescription' => 0
            ]
        ];

        $prodStmt = $pdo->prepare("INSERT INTO products (sku, barcode, name, generic_name, category_id, unit_id, dosage, manufacturer, buy_price, sell_price, min_stock, stock_quantity, requires_prescription) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) 
            ON DUPLICATE KEY UPDATE name = VALUES(name), sell_price = VALUES(sell_price), stock_quantity = VALUES(stock_quantity)");

        foreach ($products as $p) {
            $prodStmt->execute([
                $p['sku'], $p['barcode'], $p['name'], $p['generic_name'],
                $p['category_id'], $p['unit_id'], $p['dosage'], $p['manufacturer'],
                $p['buy_price'], $p['sell_price'], $p['min_stock'], $p['stock_quantity'],
                $p['requires_prescription']
            ]);
        }
        echo "Products seeded successfully.\n";

        // Get Product IDs
        $prodMap = $pdo->query("SELECT sku, id FROM products")->fetchAll(PDO::FETCH_KEY_PAIR);

        // 5. Seed Batches (FEFO demonstration)
        $batches = [
            // Amoxicillin - Critical Batch (expires in 19 days)
            [
                'product_id' => $prodMap['MED-AMX-500'] ?? 1,
                'batch_number' => 'LOT-2024-C81',
                'expiry_date' => date('Y-m-d', strtotime('+19 days')),
                'manufacture_date' => date('Y-m-d', strtotime('-18 months')),
                'initial_quantity' => 50,
                'current_quantity' => 45,
                'buy_price' => 14000.00,
                'supplier_id' => $supMap['SUP-KF-001'] ?? 1,
                'received_date' => date('Y-m-d', strtotime('-6 months'))
            ],
            // Amoxicillin - Safe Batch (expires in 14 months)
            [
                'product_id' => $prodMap['MED-AMX-500'] ?? 1,
                'batch_number' => 'LOT-2025-A02',
                'expiry_date' => date('Y-m-d', strtotime('+14 months')),
                'manufacture_date' => date('Y-m-d', strtotime('-2 months')),
                'initial_quantity' => 80,
                'current_quantity' => 80,
                'buy_price' => 14000.00,
                'supplier_id' => $supMap['SUP-KF-001'] ?? 1,
                'received_date' => date('Y-m-d', strtotime('-10 days'))
            ],
            // Paracetamol - Warning Batch (expires in 57 days)
            [
                'product_id' => $prodMap['MED-PCT-650'] ?? 2,
                'batch_number' => 'LOT-2025-P12',
                'expiry_date' => date('Y-m-d', strtotime('+57 days')),
                'manufacture_date' => date('Y-m-d', strtotime('-12 months')),
                'initial_quantity' => 100,
                'current_quantity' => 80,
                'buy_price' => 8500.00,
                'supplier_id' => $supMap['SUP-ENS-002'] ?? 2,
                'received_date' => date('Y-m-d', strtotime('-4 months'))
            ],
            // Paracetamol - Safe Batch
            [
                'product_id' => $prodMap['MED-PCT-650'] ?? 2,
                'batch_number' => 'LOT-2025-P88',
                'expiry_date' => date('Y-m-d', strtotime('+20 months')),
                'manufacture_date' => date('Y-m-d', strtotime('-1 month')),
                'initial_quantity' => 130,
                'current_quantity' => 130,
                'buy_price' => 8500.00,
                'supplier_id' => $supMap['SUP-ENS-002'] ?? 2,
                'received_date' => date('Y-m-d', strtotime('-5 days'))
            ],
            // Omeprazole - Safe Batch
            [
                'product_id' => $prodMap['MED-OMP-020'] ?? 3,
                'batch_number' => 'LOT-2025-F99',
                'expiry_date' => date('Y-m-d', strtotime('+369 days')),
                'manufacture_date' => date('Y-m-d', strtotime('-3 months')),
                'initial_quantity' => 100,
                'current_quantity' => 80,
                'buy_price' => 32000.00,
                'supplier_id' => $supMap['SUP-AAM-003'] ?? 3,
                'received_date' => date('Y-m-d', strtotime('-1 month'))
            ]
        ];

        $batchStmt = $pdo->prepare("INSERT INTO batches (product_id, batch_number, expiry_date, manufacture_date, initial_quantity, current_quantity, buy_price, supplier_id, received_date) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

        // Clear existing demo batches to prevent duplicates
        $pdo->exec("DELETE FROM batches");

        foreach ($batches as $b) {
            $batchStmt->execute([
                $b['product_id'], $b['batch_number'], $b['expiry_date'], $b['manufacture_date'],
                $b['initial_quantity'], $b['current_quantity'], $b['buy_price'], $b['supplier_id'],
                $b['received_date']
            ]);
        }
        echo "FEFO Batches seeded successfully.\n";
    }
};
