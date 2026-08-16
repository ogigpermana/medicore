<?php

namespace App\Models;

use Core\Model;
use Core\Database;
use PDO;

class StockOpname extends Model
{
    protected string $table = 'stock_opnames';
    protected string $primaryKey = 'id';

    public function __construct(?Database $db = null)
    {
        parent::__construct($db);
    }

    /**
     * Generate Stock Opname Number
     * e.g. SO-20260816-0001
     */
    public function generateNumber(): string
    {
        $datePrefix = date('Ymd');
        $sql = "SELECT opname_number FROM {$this->table} 
                WHERE opname_number LIKE ? 
                ORDER BY id DESC LIMIT 1";
        $last = $this->db->fetch($sql, ["SO-{$datePrefix}-%"]);

        if ($last && !empty($last['opname_number'])) {
            $parts = explode('-', $last['opname_number']);
            $seq = (int)end($parts) + 1;
        } else {
            $seq = 1;
        }

        return sprintf("SO-%s-%04d", $datePrefix, $seq);
    }

    /**
     * Get list of Stock Opnames
     */
    public function getList(?string $status = null): array
    {
        $sql = "SELECT so.*, u.full_name as creator_name, ap.full_name as approver_name
                FROM {$this->table} so
                JOIN users u ON so.user_id = u.id
                LEFT JOIN users ap ON so.approved_by = ap.id
                WHERE 1=1";
        $params = [];

        if ($status && $status !== 'all') {
            $sql .= " AND so.status = ?";
            $params[] = $status;
        }

        $sql .= " ORDER BY so.id DESC";

        return $this->db->query($sql, $params);
    }

    /**
     * Get single Stock Opname with items
     */
    public function getDetails(int $id): ?array
    {
        $sql = "SELECT so.*, u.full_name as creator_name, ap.full_name as approver_name
                FROM {$this->table} so
                JOIN users u ON so.user_id = u.id
                LEFT JOIN users ap ON so.approved_by = ap.id
                WHERE so.id = ?";
        $so = $this->db->fetch($sql, [$id]);

        if (!$so) {
            return null;
        }

        $itemSql = "SELECT soi.*, p.name as product_name, p.sku, p.barcode, u.symbol as unit_symbol,
                           c.name as category_name, b.batch_number, b.expiry_date
                    FROM stock_opname_items soi
                    JOIN products p ON soi.product_id = p.id
                    LEFT JOIN units u ON p.unit_id = u.id
                    LEFT JOIN categories c ON p.category_id = c.id
                    LEFT JOIN batches b ON soi.batch_id = b.id
                    WHERE soi.stock_opname_id = ?
                    ORDER BY p.name ASC";
        $so['items'] = $this->db->query($itemSql, [$id]);

        return $so;
    }

    /**
     * Initialize a new Stock Opname session from current inventory
     */
    public function startOpname(string $title, int $userId, ?int $categoryId = null, ?string $notes = null): int
    {
        $pdo = $this->db->connect();
        $pdo->beginTransaction();

        try {
            $opnameNumber = $this->generateNumber();

            $stmt = $pdo->prepare(
                "INSERT INTO {$this->table} (opname_number, title, status, user_id, notes, created_at)
                 VALUES (?, ?, 'in_progress', ?, ?, NOW())"
            );
            $stmt->execute([$opnameNumber, $title, $userId, $notes]);
            $opnameId = (int)$pdo->lastInsertId();

            // Load products from inventory to count
            $prodSql = "SELECT p.id as product_id, p.stock_quantity, p.buy_price 
                        FROM products p 
                        WHERE p.is_active = 1";
            $params = [];
            if ($categoryId) {
                $prodSql .= " AND p.category_id = ?";
                $params[] = $categoryId;
            }
            $prodSql .= " ORDER BY p.name ASC";

            $prodStmt = $pdo->prepare($prodSql);
            $prodStmt->execute($params);
            $products = $prodStmt->fetchAll(PDO::FETCH_ASSOC);

            $itemStmt = $pdo->prepare(
                "INSERT INTO stock_opname_items 
                 (stock_opname_id, product_id, system_qty, physical_qty, variance_qty, buy_price, variance_value, adjustment_reason)
                 VALUES (?, ?, ?, ?, 0, ?, 0.00, 'matched')"
            );

            $totalSysQty = 0;
            foreach ($products as $p) {
                $sysQty = (int)$p['stock_quantity'];
                $itemStmt->execute([
                    $opnameId,
                    $p['product_id'],
                    $sysQty,
                    $sysQty, // Default physical count matches system initially
                    (float)$p['buy_price']
                ]);
                $totalSysQty += $sysQty;
            }

            // Update initial counts on parent record
            $upStmt = $pdo->prepare(
                "UPDATE {$this->table} SET total_items_counted = ?, total_system_qty = ?, total_physical_qty = ? WHERE id = ?"
            );
            $upStmt->execute([count($products), $totalSysQty, $totalSysQty, $opnameId]);

            $pdo->commit();
            return $opnameId;
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Save physical count updates
     */
    public function saveCounts(int $opnameId, array $items): bool
    {
        $pdo = $this->db->connect();
        $pdo->beginTransaction();

        try {
            $updateItemStmt = $pdo->prepare(
                "UPDATE stock_opname_items 
                 SET physical_qty = ?, variance_qty = ?, variance_value = ?, adjustment_reason = ?, notes = ?, updated_at = NOW()
                 WHERE id = ? AND stock_opname_id = ?"
            );

            $totalPhysical = 0;
            $totalVarianceQty = 0;
            $totalVarianceValue = 0.0;

            foreach ($items as $it) {
                $physicalQty = (int)$it['physical_qty'];
                $systemQty = (int)$it['system_qty'];
                $varianceQty = $physicalQty - $systemQty;
                $buyPrice = (float)$it['buy_price'];
                $varianceValue = $varianceQty * $buyPrice;

                $updateItemStmt->execute([
                    $physicalQty,
                    $varianceQty,
                    $varianceValue,
                    $it['adjustment_reason'] ?? 'matched',
                    $it['notes'] ?? null,
                    $it['id'],
                    $opnameId
                ]);

                $totalPhysical += $physicalQty;
                $totalVarianceQty += $varianceQty;
                $totalVarianceValue += $varianceValue;
            }

            $upParentStmt = $pdo->prepare(
                "UPDATE {$this->table} 
                 SET total_physical_qty = ?, total_variance_qty = ?, total_variance_value = ?, updated_at = NOW()
                 WHERE id = ?"
            );
            $upParentStmt->execute([$totalPhysical, $totalVarianceQty, $totalVarianceValue, $opnameId]);

            $pdo->commit();
            return true;
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Approve and finalize Stock Opname: Auto-adjust product stock & FEFO batches and log stock movements!
     */
    public function approveAndReconcile(int $opnameId, int $approverId): bool
    {
        $pdo = $this->db->connect();
        $pdo->beginTransaction();

        try {
            $so = $this->getDetails($opnameId);
            if (!$so || $so['status'] === 'completed') {
                $pdo->rollBack();
                return false;
            }

            $stockUpdateStmt = $pdo->prepare("UPDATE products SET stock_quantity = ? WHERE id = ?");
            $movementStmt = $pdo->prepare(
                "INSERT INTO stock_movements 
                 (product_id, batch_id, type, quantity, balance_after, reference_type, reference_id, notes, user_id)
                 VALUES (?, NULL, ?, ?, ?, 'stock_opname', ?, ?, ?)"
            );

            foreach ($so['items'] as $it) {
                $variance = (int)$it['variance_qty'];
                if ($variance === 0) continue;

                $prodId = (int)$it['product_id'];
                $physicalQty = (int)$it['physical_qty'];
                $movType = $variance > 0 ? 'adjust_plus' : 'adjust_minus';
                $movNotes = "Stock Opname {$so['opname_number']}: " . ucfirst($it['adjustment_reason']) . ($it['notes'] ? " ({$it['notes']})" : "");

                // 1. Update product master stock
                $stockUpdateStmt->execute([$physicalQty, $prodId]);

                // 2. Log immutable stock movement
                $movementStmt->execute([
                    $prodId,
                    $movType,
                    abs($variance),
                    $physicalQty,
                    $opnameId,
                    $movNotes,
                    $approverId
                ]);
            }

            // Mark Stock Opname as completed
            $completeStmt = $pdo->prepare(
                "UPDATE {$this->table} 
                 SET status = 'completed', approved_by = ?, completed_at = NOW(), updated_at = NOW() 
                 WHERE id = ?"
            );
            $completeStmt->execute([$approverId, $opnameId]);

            $pdo->commit();
            return true;
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}
