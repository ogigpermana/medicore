<?php

/**
 * Seeder: Seed Purchasing & PBF PO Module
 * Description: Populates realistic purchase orders (SP Reguler, SP Prekursor, SP OOT), Goods Receipts & AP Ledger
 */

return new class {
    public function run(PDO $pdo): void
    {
        // 1. Check if purchase orders already exist
        $stmt = $pdo->query("SELECT COUNT(*) FROM purchase_orders");
        if ((int)$stmt->fetchColumn() > 0) {
            echo "Purchase orders already seeded.\n";
            return;
        }

        // Get pharmacist user id
        $userStmt = $pdo->query("SELECT id FROM users WHERE email = 'pharmacist@medicore.com' LIMIT 1");
        $pharmacistId = (int)($userStmt->fetchColumn() ?: 1);

        // Get suppliers
        $suppliers = $pdo->query("SELECT id, name FROM suppliers ORDER BY id ASC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
        if (empty($suppliers)) {
            echo "No suppliers found for purchasing seed.\n";
            return;
        }

        // Get products
        $products = $pdo->query("SELECT id, name, buy_price FROM products ORDER BY id ASC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
        if (empty($products)) {
            echo "No products found for purchasing seed.\n";
            return;
        }

        $today = date('Ymd');

        // 1. PO 1: SP Reguler to PT Kimia Farma TD (Status: Received with GRN & AP Invoice)
        $po1Number = 'SP-REG-' . $today . '-0001';
        $po1Stmt = $pdo->prepare("INSERT INTO purchase_orders (
            po_number, sp_type, supplier_id, user_id, order_date, expected_delivery_date,
            status, payment_terms, subtotal, discount_amount, tax_amount, grand_total, notes, pharmacist_sipa, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");

        $po1Stmt->execute([
            $po1Number,
            'regular',
            $suppliers[0]['id'],
            $pharmacistId,
            date('Y-m-d', strtotime('-5 days')),
            date('Y-m-d', strtotime('-2 days')),
            'received',
            'net_30',
            2500000.00,
            0.00,
            275000.00,
            2775000.00,
            'Surat Pesanan Obat Reguler untuk Restock Bulanan Apotek.',
            'SIPA: 19880415/SIPA_32.73/2022/2001'
        ]);
        $po1Id = (int)$pdo->lastInsertId();

        // PO 1 items
        $poiStmt = $pdo->prepare("INSERT INTO purchase_order_items (
            purchase_order_id, product_id, quantity_ordered, quantity_received, unit_price, discount_percent, tax_percent, subtotal, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");

        $poiStmt->execute([$po1Id, $products[0]['id'], 100, 100, (float)$products[0]['buy_price'], 0, 0, 100 * (float)$products[0]['buy_price']]);
        $poiStmt->execute([$po1Id, $products[1]['id'], 50, 50, (float)$products[1]['buy_price'], 0, 0, 50 * (float)$products[1]['buy_price']]);
        $poiStmt->execute([$po1Id, $products[2]['id'], 40, 40, (float)$products[2]['buy_price'], 0, 0, 40 * (float)$products[2]['buy_price']]);

        // Goods Receipt 1 (Faktur PBF Received)
        $grn1Number = 'GRN-' . $today . '-0001';
        $grStmt = $pdo->prepare("INSERT INTO goods_receipts (
            grn_number, purchase_order_id, supplier_id, received_by, invoice_number, invoice_date,
            due_date, subtotal, tax_amount, total_amount, amount_paid, payment_status, notes, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");

        $grStmt->execute([
            $grn1Number,
            $po1Id,
            $suppliers[0]['id'],
            $pharmacistId,
            'FAK-KF/' . date('Ym') . '/0892',
            date('Y-m-d', strtotime('-3 days')),
            date('Y-m-d', strtotime('+27 days')),
            2500000.00,
            275000.00,
            2775000.00,
            1000000.00,
            'partial',
            'Barang diterima lengkap dengan kondisi segel pabrik utuh & CoA terlampir.'
        ]);
        $gr1Id = (int)$pdo->lastInsertId();

        // Create Batches & Goods Receipt Items
        $batchStmt = $pdo->prepare("INSERT INTO batches (
            product_id, batch_number, expiry_date, initial_quantity, current_quantity, buy_price, supplier_id, received_date, is_active
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)");

        $griStmt = $pdo->prepare("INSERT INTO goods_receipt_items (
            goods_receipt_id, product_id, batch_number, expiry_date, quantity_received, buy_price, subtotal, batch_id, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");

        // Batch 1
        $batchStmt->execute([$products[0]['id'], 'KF-' . date('ymd') . '-A', date('Y-m-d', strtotime('+24 months')), 100, 100, (float)$products[0]['buy_price'], $suppliers[0]['id'], date('Y-m-d', strtotime('-3 days'))]);
        $b1Id = (int)$pdo->lastInsertId();
        $griStmt->execute([$gr1Id, $products[0]['id'], 'KF-' . date('ymd') . '-A', date('Y-m-d', strtotime('+24 months')), 100, (float)$products[0]['buy_price'], 100 * (float)$products[0]['buy_price'], $b1Id]);

        // Batch 2
        $batchStmt->execute([$products[1]['id'], 'KF-' . date('ymd') . '-B', date('Y-m-d', strtotime('+18 months')), 50, 50, (float)$products[1]['buy_price'], $suppliers[0]['id'], date('Y-m-d', strtotime('-3 days'))]);
        $b2Id = (int)$pdo->lastInsertId();
        $griStmt->execute([$gr1Id, $products[1]['id'], 'KF-' . date('ymd') . '-B', date('Y-m-d', strtotime('+18 months')), 50, (float)$products[1]['buy_price'], 50 * (float)$products[1]['buy_price'], $b2Id]);

        // Batch 3
        $batchStmt->execute([$products[2]['id'], 'KF-' . date('ymd') . '-C', date('Y-m-d', strtotime('+36 months')), 40, 40, (float)$products[2]['buy_price'], $suppliers[0]['id'], date('Y-m-d', strtotime('-3 days'))]);
        $b3Id = (int)$pdo->lastInsertId();
        $griStmt->execute([$gr1Id, $products[2]['id'], 'KF-' . date('ymd') . '-C', date('Y-m-d', strtotime('+36 months')), 40, (float)$products[2]['buy_price'], 40 * (float)$products[2]['buy_price'], $b3Id]);

        // Seed AP Payment (Partial Payment)
        $apStmt = $pdo->prepare("INSERT INTO ap_payments (
            goods_receipt_id, payment_date, amount_paid, payment_method, reference_number, notes, created_by, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
        $apStmt->execute([
            $gr1Id,
            date('Y-m-d', strtotime('-1 days')),
            1000000.00,
            'bank_transfer',
            'TRF-BCA-' . $today . '-001',
            'Uang muka pembayaran 30% faktur Kimia Farma',
            $pharmacistId
        ]);

        // 2. PO 2: SP Prekursor (In Transit / Ordered)
        if (isset($suppliers[1])) {
            $po2Number = 'SP-PRK-' . $today . '-0001';
            $po2Stmt = $pdo->prepare("INSERT INTO purchase_orders (
                po_number, sp_type, supplier_id, user_id, order_date, expected_delivery_date,
                status, payment_terms, subtotal, discount_amount, tax_amount, grand_total, notes, pharmacist_sipa, created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");

            $po2Stmt->execute([
                $po2Number,
                'precursor',
                $suppliers[1]['id'],
                $pharmacistId,
                date('Y-m-d', strtotime('-1 days')),
                date('Y-m-d', strtotime('+2 days')),
                'ordered',
                'net_14',
                1800000.00,
                0.00,
                198000.00,
                1998000.00,
                'Surat Pesanan Obat Mengandung Prekursor Farmasi (Pseudoephedrine).',
                'SIPA: 19880415/SIPA_32.73/2022/2001'
            ]);
            $po2Id = (int)$pdo->lastInsertId();

            $p3 = $products[3] ?? $products[0];
            $p4 = $products[4] ?? $products[1];
            $poiStmt->execute([$po2Id, $p3['id'], 60, 0, (float)$p3['buy_price'], 0, 0, 60 * (float)$p3['buy_price']]);
            $poiStmt->execute([$po2Id, $p4['id'], 30, 0, (float)$p4['buy_price'], 0, 0, 30 * (float)$p4['buy_price']]);
        }

        // 3. PO 3: SP Obat-Obat Tertentu (OOT)
        if (isset($suppliers[2])) {
            $po3Number = 'SP-OOT-' . $today . '-0001';
            $po3Stmt = $pdo->prepare("INSERT INTO purchase_orders (
                po_number, sp_type, supplier_id, user_id, order_date, expected_delivery_date,
                status, payment_terms, subtotal, discount_amount, tax_amount, grand_total, notes, pharmacist_sipa, created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");

            $po3Stmt->execute([
                $po3Number,
                'oot',
                $suppliers[2]['id'],
                $pharmacistId,
                date('Y-m-d'),
                date('Y-m-d', strtotime('+3 days')),
                'ordered',
                'net_30',
                3200000.00,
                50000.00,
                346500.00,
                3496500.00,
                'Surat Pesanan Obat-Obat Tertentu (OOT) sesuai regulasi BPOM No. 10 Tahun 2019.',
                'SIPA: 19880415/SIPA_32.73/2022/2001'
            ]);
            $po3Id = (int)$pdo->lastInsertId();

            $poiStmt->execute([$po3Id, $products[2]['id'], 50, 0, (float)$products[2]['buy_price'], 0, 0, 50 * (float)$products[2]['buy_price']]);
            $poiStmt->execute([$po3Id, $products[1]['id'], 80, 0, (float)$products[1]['buy_price'], 0, 0, 80 * (float)$products[1]['buy_price']]);
        }

        echo "Purchasing module (PO, BPOM SP, GRN, Batches, AP Payments) seeded successfully.\n";
    }
};
