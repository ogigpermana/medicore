<?php

/**
 * Seeder: Seed Pharmacy Branches and Inter-Branch Stock Transfers
 */

return new class {
    public function run(PDO $pdo): void
    {
        // 1. Seed Branches
        $branchCount = (int)$pdo->query("SELECT COUNT(*) FROM branches")->fetchColumn();
        if ($branchCount === 0) {
            $branches = [
                [
                    'code' => 'CB-PST',
                    'name' => 'Apotek Central Branch (Pusat)',
                    'type' => 'main_branch',
                    'phone' => '021-5550199',
                    'email' => 'pusat@medicore.com',
                    'address' => 'Jl. Farmasi Sehat No. 1, Gambir, Jakarta Pusat',
                    'pharmacist_in_charge' => 'apt. MediCore APJ, S.Farm.',
                    'sipa_number' => '19880415/SIPA_32.73/2022/2001',
                    'sia_number' => '503/001/SIA/DPMPTSP/2023'
                ],
                [
                    'code' => 'CB-BRT',
                    'name' => 'Apotek Cabang Barat',
                    'type' => 'sub_branch',
                    'phone' => '021-5550288',
                    'email' => 'barat@medicore.com',
                    'address' => 'Jl. Boulevard Barat Raya Blok LB No. 88, Kebon Jeruk, Jakarta Barat',
                    'pharmacist_in_charge' => 'apt. Rian Pratama, S.Farm.',
                    'sipa_number' => '19920810/SIPA_31.73/2023/2045',
                    'sia_number' => '503/045/SIA/DPMPTSP/2023'
                ],
                [
                    'code' => 'CB-TMR',
                    'name' => 'Apotek Cabang Timur',
                    'type' => 'sub_branch',
                    'phone' => '021-5550377',
                    'email' => 'timur@medicore.com',
                    'address' => 'Jl. Raya Pemuda No. 42, Rawamangun, Jakarta Timur',
                    'pharmacist_in_charge' => 'apt. Siti Rahmawati, S.Farm.',
                    'sipa_number' => '19950312/SIPA_31.75/2024/1089',
                    'sia_number' => '503/089/SIA/DPMPTSP/2024'
                ],
                [
                    'code' => 'GD-PST',
                    'name' => 'Gudang Logistik Pusat (DC)',
                    'type' => 'warehouse',
                    'phone' => '021-8889900',
                    'email' => 'gudang.pusat@medicore.com',
                    'address' => 'Kawasan Industri Sentra Farmasi Blok C-12, Cikarang, Bekasi',
                    'pharmacist_in_charge' => 'apt. Budi Santoso, S.Farm.',
                    'sipa_number' => '19871105/SIPA_32.16/2021/3012',
                    'sia_number' => '503/112/SIA/DPMPTSP/2021'
                ]
            ];

            $bStmt = $pdo->prepare(
                "INSERT INTO branches (code, name, type, phone, email, address, pharmacist_in_charge, sipa_number, sia_number, is_active, created_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1, NOW())"
            );

            foreach ($branches as $b) {
                $bStmt->execute([
                    $b['code'],
                    $b['name'],
                    $b['type'],
                    $b['phone'],
                    $b['email'],
                    $b['address'],
                    $b['pharmacist_in_charge'],
                    $b['sipa_number'],
                    $b['sia_number']
                ]);
            }
            echo "Branches seeded successfully.\n";
        }

        // 2. Seed Stock Transfers
        $trfCount = (int)$pdo->query("SELECT COUNT(*) FROM stock_transfers")->fetchColumn();
        if ($trfCount === 0) {
            $branchRows = $pdo->query("SELECT id, code FROM branches")->fetchAll(PDO::FETCH_KEY_PAIR);
            $mainBranchId = (int)($branchRows['CB-PST'] ?? 1);
            $baratBranchId = (int)($branchRows['CB-BRT'] ?? 2);
            $timurBranchId = (int)($branchRows['CB-TMR'] ?? 3);
            $dcWarehouseId = (int)($branchRows['GD-PST'] ?? 4);

            $userStmt = $pdo->query("SELECT id FROM users LIMIT 1");
            $userId = (int)($userStmt->fetchColumn() ?: 1);

            $products = $pdo->query("SELECT id, name, buy_price FROM products LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
            $today = date('Ymd');

            if (!empty($products)) {
                // Transfer 1: Received
                $trf1Number = "TRF-{$today}-0001";
                $t1Stmt = $pdo->prepare(
                    "INSERT INTO stock_transfers 
                     (transfer_number, source_branch_id, destination_branch_id, status, requested_by, approved_by, received_by, total_items, total_qty_sent, total_qty_received, driver_name, vehicle_number, shipping_notes, departure_date, received_date, created_at)
                     VALUES (?, ?, ?, 'received', ?, ?, ?, 2, 50, 50, 'Ahmad Kurir', 'B 1234 FAR', 'Pengiriman reguler antar cabang Jakarta Barat.', DATE_SUB(NOW(), INTERVAL 2 HOUR), NOW(), DATE_SUB(NOW(), INTERVAL 3 HOUR))"
                );
                $t1Stmt->execute([$trf1Number, $mainBranchId, $baratBranchId, $userId, $userId, $userId]);
                $trf1Id = (int)$pdo->lastInsertId();

                $itemStmt = $pdo->prepare(
                    "INSERT INTO stock_transfer_items 
                     (stock_transfer_id, product_id, batch_id, qty_requested, qty_sent, qty_received, unit_buy_price, notes, created_at)
                     VALUES (?, ?, NULL, ?, ?, ?, ?, 'Kondisi kemasan baik & tersegel', NOW())"
                );

                $itemStmt->execute([$trf1Id, $products[0]['id'], 30, 30, 30, (float)$products[0]['buy_price']]);
                if (isset($products[1])) {
                    $itemStmt->execute([$trf1Id, $products[1]['id'], 20, 20, 20, (float)$products[1]['buy_price']]);
                }

                // Transfer 2: In-Transit (Sedang Dikirim)
                $trf2Number = "TRF-{$today}-0002";
                $t2Stmt = $pdo->prepare(
                    "INSERT INTO stock_transfers 
                     (transfer_number, source_branch_id, destination_branch_id, status, requested_by, approved_by, received_by, total_items, total_qty_sent, total_qty_received, driver_name, vehicle_number, shipping_notes, departure_date, created_at)
                     VALUES (?, ?, ?, 'in_transit', ?, ?, NULL, 2, 40, 0, 'Bambang Logistik', 'B 5678 MED', 'Pengiriman batch darurat ke Cabang Timur via mobil box berpendingin.', NOW(), NOW())"
                );
                $t2Stmt->execute([$trf2Number, $mainBranchId, $timurBranchId, $userId, $userId]);
                $trf2Id = (int)$pdo->lastInsertId();

                if (isset($products[2])) {
                    $itemStmt->execute([$trf2Id, $products[2]['id'], 25, 25, 0, (float)$products[2]['buy_price']]);
                }
                if (isset($products[3])) {
                    $itemStmt->execute([$trf2Id, $products[3]['id'], 15, 15, 0, (float)$products[3]['buy_price']]);
                }

                // Transfer 3: Pending Approval
                $trf3Number = "TRF-{$today}-0003";
                $t3Stmt = $pdo->prepare(
                    "INSERT INTO stock_transfers 
                     (transfer_number, source_branch_id, destination_branch_id, status, requested_by, approved_by, received_by, total_items, total_qty_sent, total_qty_received, shipping_notes, created_at)
                     VALUES (?, ?, ?, 'pending_approval', ?, NULL, NULL, 1, 100, 100, 'Permintaan stok tambahan dari Gudang Logistik Pusat untuk persiapan akhir pekan.', NOW())"
                );
                $t3Stmt->execute([$trf3Number, $dcWarehouseId, $mainBranchId, $userId]);
                $trf3Id = (int)$pdo->lastInsertId();

                $itemStmt->execute([$trf3Id, $products[0]['id'], 100, 100, 0, (float)$products[0]['buy_price']]);

                echo "Stock transfers seeded successfully.\n";
            }
        }
    }
};
