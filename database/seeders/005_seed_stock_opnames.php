<?php

/**
 * Seeder: Seed Stock Opname Sessions
 * Description: Populates completed and in-progress physical stock count sessions with variances
 */

return new class {
    public function run(PDO $pdo): void
    {
        $stmt = $pdo->query("SELECT COUNT(*) FROM stock_opnames");
        if ((int)$stmt->fetchColumn() > 0) {
            echo "Stock opnames already seeded.\n";
            return;
        }

        // Get user ids
        $pharmacistStmt = $pdo->query("SELECT id FROM users WHERE email = 'pharmacist@medicore.com' LIMIT 1");
        $pharmacistId = (int)($pharmacistStmt->fetchColumn() ?: 1);

        $superadminStmt = $pdo->query("SELECT id FROM users WHERE email = 'superadmin@medicore.com' LIMIT 1");
        $adminId = (int)($superadminStmt->fetchColumn() ?: 1);

        // Get products
        $products = $pdo->query("SELECT id, name, sku, stock_quantity, buy_price FROM products ORDER BY id ASC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
        if (empty($products)) {
            echo "No products found for stock opname seed.\n";
            return;
        }

        $today = date('Ymd');

        // 1. Completed Stock Opname: "Stock Opname Bulanan Etalase Depan"
        $so1Number = 'SO-' . $today . '-0001';
        $so1Stmt = $pdo->prepare(
            "INSERT INTO stock_opnames 
             (opname_number, title, status, user_id, approved_by, total_items_counted, total_system_qty, total_physical_qty, total_variance_qty, total_variance_value, notes, completed_at, created_at)
             VALUES (?, ?, 'completed', ?, ?, 5, 250, 248, -2, -30000.00, 'Stock opname rutin bulanan etalase depan. Selisih 2 tablet rusak/pecah saat display.', NOW(), NOW())"
        );
        $so1Stmt->execute([$so1Number, 'Stock Opname Bulanan Etalase Depan', $pharmacistId, $adminId]);
        $so1Id = (int)$pdo->lastInsertId();

        $soiStmt = $pdo->prepare(
            "INSERT INTO stock_opname_items 
             (stock_opname_id, product_id, system_qty, physical_qty, variance_qty, buy_price, variance_value, adjustment_reason, notes, created_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())"
        );

        // Item 1: Matched
        $p1 = $products[0];
        $soiStmt->execute([$so1Id, $p1['id'], (int)$p1['stock_quantity'], (int)$p1['stock_quantity'], 0, (float)$p1['buy_price'], 0.00, 'matched', 'Fisik dan sistem cocok']);

        // Item 2: Damaged (-2 units)
        if (isset($products[1])) {
            $p2 = $products[1];
            $sys = (int)$p2['stock_quantity'];
            $phys = max(0, $sys - 2);
            $var = $phys - $sys;
            $soiStmt->execute([$so1Id, $p2['id'], $sys, $phys, $var, (float)$p2['buy_price'], $var * (float)$p2['buy_price'], 'damaged', '2 strip kemasan blister penyok/rusak']);
        }

        // 2. In-Progress Stock Opname: "Stock Opname Gudang Obat Keras & Antibiotik"
        $so2Number = 'SO-' . $today . '-0002';
        $so2Stmt = $pdo->prepare(
            "INSERT INTO stock_opnames 
             (opname_number, title, status, user_id, approved_by, total_items_counted, total_system_qty, total_physical_qty, total_variance_qty, total_variance_value, notes, created_at)
             VALUES (?, ?, 'in_progress', ?, NULL, 4, 180, 180, 0, 0.00, 'Audit fisik stok gudang antibiotik dan obat keras.', NOW())"
        );
        $so2Stmt->execute([$so2Number, 'Stock Opname Gudang Obat Keras & Antibiotik', $pharmacistId]);
        $so2Id = (int)$pdo->lastInsertId();

        for ($i = 2; $i < min(6, count($products)); $i++) {
            $p = $products[$i];
            $soiStmt->execute([$so2Id, $p['id'], (int)$p['stock_quantity'], (int)$p['stock_quantity'], 0, (float)$p['buy_price'], 0.00, 'matched', 'Sedang dihitung']);
        }

        echo "Stock Opname module seeded successfully.\n";
    }
};
