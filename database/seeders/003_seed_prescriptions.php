<?php

/**
 * Seeder: Seed Clinical Digital Prescriptions
 * Description: Populates realistic doctor prescriptions with finished medications and compounding mixtures
 */

return new class {
    public function run(PDO $pdo): void
    {
        // Get Pharmacist User ID
        $userStmt = $pdo->query("SELECT id FROM users WHERE email = 'pharmacist@medicore.com' LIMIT 1");
        $pharmacist = $userStmt->fetch(PDO::FETCH_ASSOC);
        $pharmacistId = $pharmacist ? (int)$pharmacist['id'] : null;

        // Get Products
        $pStmt = $pdo->query("SELECT id, sku, sell_price FROM products");
        $products = [];
        while ($row = $pStmt->fetch(PDO::FETCH_ASSOC)) {
            $products[$row['sku']] = $row;
        }

        // 1. Prescription 1: Pediatric Bronchitis with Finished Amoxicillin + Puyer Batuk
        $today = date('Ymd');
        $rx1Number = 'RX-' . $today . '-0001';
        $check1 = $pdo->prepare("SELECT id FROM prescriptions WHERE prescription_number = ?");
        $check1->execute([$rx1Number]);

        if (!$check1->fetch()) {
            $amx = $products['MED-AMX-500'] ?? null;
            $pcm = $products['MED-PCM-500'] ?? null;
            $ctm = $products['MED-CTM-004'] ?? null;

            $total = 0.00;
            $tuslah = 5000.00;
            $embalase = 3000.00;

            if ($amx) $total += (float)$amx['sell_price'] * 1; // 1 strip
            $compSubtotal = 0.00;
            if ($pcm) $compSubtotal += (float)$pcm['sell_price'] * 0.5 * 10;
            if ($ctm) $compSubtotal += (float)$ctm['sell_price'] * 0.25 * 10;
            $total += $compSubtotal + $tuslah + $embalase;

            $stmt1 = $pdo->prepare("INSERT INTO prescriptions (
                prescription_number, patient_name, patient_age, patient_gender, patient_weight,
                doctor_name, doctor_sip, doctor_clinic, diagnosis, clinical_notes,
                status, pharmacist_id, pharmacist_notes, total_amount, tuslah_fee, embalase_fee,
                reviewed_at, created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");

            $stmt1->execute([
                $rx1Number,
                'Ananda Reyhan Pratama',
                7,
                'male',
                22.50,
                'dr. Sarah Sp.A',
                'SIP: 503/446/SIP-D/2024',
                'Klinik Pratama Sehat Bersama',
                'Acute Bronchitis & Febris',
                'Pasien demam 3 hari disertai batuk berdahak. Tidak ada riwayat alergi penisilin.',
                'reviewed',
                $pharmacistId,
                'Dosis amoxicillin dan puyer sesuai dengan BB anak (22.5 kg). Telah diverifikasi.',
                $total,
                $tuslah,
                $embalase
            ]);

            $rx1Id = (int)$pdo->lastInsertId();

            // Insert Finished item (Amoxicillin)
            if ($amx) {
                $itemStmt = $pdo->prepare("INSERT INTO prescription_items (
                    prescription_id, product_id, dosage_instructions, usage_time, quantity, unit_price, total_price, notes
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $itemStmt->execute([
                    $rx1Id,
                    $amx['id'],
                    '3x sehari 1 sendok takar / tablet',
                    'Sesudah Makan (Harus Dihabiskan)',
                    1,
                    $amx['sell_price'],
                    $amx['sell_price'],
                    'Antibiotik lini pertama - habiskan sampai hari ke-5'
                ]);
            }

            // Insert Compound (Puyer Batuk Anak)
            $compStmt = $pdo->prepare("INSERT INTO prescription_compounds (
                prescription_id, compound_name, packaging_type, quantity_pack, dosage_instructions, compounding_fee, packaging_fee, total_price, notes
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

            $compStmt->execute([
                $rx1Id,
                'Puyer Batuk Pilek Anak No. X',
                'puyer',
                10,
                '3x sehari 1 bungkus',
                $tuslah,
                $embalase,
                $compSubtotal + $tuslah + $embalase,
                'Racik halus dalam 10 bungkus perkamen kedap udara'
            ]);
            $compound1Id = (int)$pdo->lastInsertId();

            // Insert Compound Ingredients
            if ($pcm) {
                $ingStmt = $pdo->prepare("INSERT INTO prescription_compound_items (
                    compound_id, product_id, dose_per_pack, quantity_used, unit_price, subtotal
                ) VALUES (?, ?, ?, ?, ?, ?)");
                $ingStmt->execute([
                    $compound1Id,
                    $pcm['id'],
                    '250 mg',
                    5,
                    $pcm['sell_price'],
                    (float)$pcm['sell_price'] * 5
                ]);
            }
        }

        // 2. Prescription 2: Hypertension & Gastroprotection (Pending Review)
        $rx2Number = 'RX-' . $today . '-0002';
        $check2 = $pdo->prepare("SELECT id FROM prescriptions WHERE prescription_number = ?");
        $check2->execute([$rx2Number]);

        if (!$check2->fetch()) {
            $aml = $products['MED-AML-010'] ?? null;
            $omp = $products['MED-OMP-020'] ?? null;

            $total2 = 0.00;
            if ($aml) $total2 += (float)$aml['sell_price'] * 3; // 3 strips = 30 tabs
            if ($omp) $total2 += (float)$omp['sell_price'] * 2; // 2 strips = 20 caps

            $stmt2 = $pdo->prepare("INSERT INTO prescriptions (
                prescription_number, patient_name, patient_age, patient_gender, patient_weight,
                doctor_name, doctor_sip, doctor_clinic, diagnosis, clinical_notes,
                status, pharmacist_id, pharmacist_notes, total_amount, tuslah_fee, embalase_fee,
                created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");

            $stmt2->execute([
                $rx2Number,
                'Bapak Bambang Waluyo',
                58,
                'male',
                74.00,
                'dr. Hendra Sp.PD',
                'SIP: 449/112/SIP-SP/2023',
                'RS Medika Kasih',
                'Hypertension Stage 2 & GERD',
                'Rutin konsumsi antihipertensi tiap pagi. Omeprazole diminum 30 menit sebelum sarapan.',
                'pending',
                null,
                null,
                $total2,
                0.00,
                0.00
            ]);

            $rx2Id = (int)$pdo->lastInsertId();

            if ($aml) {
                $itemStmt2 = $pdo->prepare("INSERT INTO prescription_items (
                    prescription_id, product_id, dosage_instructions, usage_time, quantity, unit_price, total_price, notes
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $itemStmt2->execute([
                    $rx2Id,
                    $aml['id'],
                    '1x sehari 1 tablet',
                    'Pagi Hari Setelah Sarapan',
                    3,
                    $aml['sell_price'],
                    (float)$aml['sell_price'] * 3,
                    'Kontrol tensi berkala'
                ]);
            }

            if ($omp) {
                $itemStmt2 = $pdo->prepare("INSERT INTO prescription_items (
                    prescription_id, product_id, dosage_instructions, usage_time, quantity, unit_price, total_price, notes
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $itemStmt2->execute([
                    $rx2Id,
                    $omp['id'],
                    '1x sehari 1 kapsul',
                    '30 Menit Sebelum Sarapan Pagi',
                    2,
                    $omp['sell_price'],
                    (float)$omp['sell_price'] * 2,
                    'Gastroprotektor mukosa lambung'
                ]);
            }
        }

        echo "Sample clinical prescriptions seeded successfully.\n";
    }
};
