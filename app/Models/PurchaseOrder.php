<?php

namespace App\Models;

use Core\Model;
use Core\Database;
use PDO;

class PurchaseOrder extends Model
{
    protected string $table = 'purchase_orders';
    protected string $primaryKey = 'id';

    public function __construct(?Database $db = null)
    {
        parent::__construct($db);
    }

    /**
     * Generate unique PO Number according to BPOM SP Type
     * e.g., SP-REG-20260816-0001 or PO-20260816-0001
     */
    public function generatePoNumber(string $spType = 'regular'): string
    {
        $datePrefix = date('Ymd');
        $typeCode = match($spType) {
            'precursor' => 'SP-PRK',
            'oot' => 'SP-OOT',
            'narcotic_psychotropic' => 'SP-NKT',
            default => 'SP-REG'
        };

        $sql = "SELECT po_number FROM {$this->table} 
                WHERE po_number LIKE ? 
                ORDER BY id DESC LIMIT 1";
        $last = $this->db->fetch($sql, ["{$typeCode}-{$datePrefix}-%"]);

        if ($last && !empty($last['po_number'])) {
            $parts = explode('-', $last['po_number']);
            $seq = (int)end($parts) + 1;
        } else {
            $seq = 1;
        }

        return sprintf("%s-%s-%04d", $typeCode, $datePrefix, $seq);
    }

    /**
     * Get PO list with filter & supplier info
     */
    public function getList(?string $status = null, ?string $spType = null): array
    {
        $sql = "SELECT po.*, s.name as supplier_name, s.phone as supplier_phone, s.contact_person,
                       u.full_name as created_by_name,
                       (SELECT COUNT(*) FROM purchase_order_items WHERE purchase_order_id = po.id) as item_count,
                       (SELECT COUNT(*) FROM goods_receipts WHERE purchase_order_id = po.id) as receipt_count
                FROM {$this->table} po
                JOIN suppliers s ON po.supplier_id = s.id
                JOIN users u ON po.user_id = u.id
                WHERE 1=1";
        $params = [];

        if ($status && $status !== 'all') {
            $sql .= " AND po.status = ?";
            $params[] = $status;
        }

        if ($spType && $spType !== 'all') {
            $sql .= " AND po.sp_type = ?";
            $params[] = $spType;
        }

        $sql .= " ORDER BY po.id DESC";

        return $this->db->query($sql, $params);
    }

    /**
     * Get full details of a single PO with items & receipts
     */
    public function getDetails(int $id): ?array
    {
        $sql = "SELECT po.*, s.name as supplier_name, s.code as supplier_code, s.phone as supplier_phone, 
                       s.email as supplier_email, s.address as supplier_address, s.contact_person,
                       u.full_name as created_by_name, u.email as created_by_email
                FROM {$this->table} po
                JOIN suppliers s ON po.supplier_id = s.id
                JOIN users u ON po.user_id = u.id
                WHERE po.id = ?";
        $po = $this->db->fetch($sql, [$id]);

        if (!$po) {
            return null;
        }

        // Fetch items
        $itemSql = "SELECT poi.*, p.name as product_name, p.sku, p.barcode, u.symbol as unit_symbol, c.name as category_name
                    FROM purchase_order_items poi
                    JOIN products p ON poi.product_id = p.id
                    LEFT JOIN units u ON p.unit_id = u.id
                    LEFT JOIN categories c ON p.category_id = c.id
                    WHERE poi.purchase_order_id = ?
                    ORDER BY poi.id ASC";
        $po['items'] = $this->db->query($itemSql, [$id]);

        // Fetch Goods Receipts for this PO
        $grSql = "SELECT gr.*, u.full_name as receiver_name
                  FROM goods_receipts gr
                  JOIN users u ON gr.received_by = u.id
                  WHERE gr.purchase_order_id = ?
                  ORDER BY gr.id DESC";
        $po['receipts'] = $this->db->query($grSql, [$id]);

        return $po;
    }

    /**
     * Create a new Purchase Order with items
     */
    public function createPurchaseOrder(array $poData, array $items): int
    {
        $pdo = $this->db->connect();
        $pdo->beginTransaction();

        try {
            $poNumber = $poData['po_number'] ?? $this->generatePoNumber($poData['sp_type'] ?? 'regular');

            $stmt = $pdo->prepare(
                "INSERT INTO {$this->table} 
                 (po_number, sp_type, supplier_id, user_id, order_date, expected_delivery_date, 
                  status, payment_terms, subtotal, discount_amount, tax_amount, grand_total, notes, pharmacist_sipa)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );

            $stmt->execute([
                $poNumber,
                $poData['sp_type'] ?? 'regular',
                $poData['supplier_id'],
                $poData['user_id'],
                $poData['order_date'] ?? date('Y-m-d'),
                $poData['expected_delivery_date'] ?? null,
                $poData['status'] ?? 'ordered',
                $poData['payment_terms'] ?? 'net_30',
                $poData['subtotal'] ?? 0.00,
                $poData['discount_amount'] ?? 0.00,
                $poData['tax_amount'] ?? 0.00,
                $poData['grand_total'] ?? 0.00,
                $poData['notes'] ?? null,
                $poData['pharmacist_sipa'] ?? 'SIPA: 19880415/SIPA_32.73/2022/2001'
            ]);

            $poId = (int)$pdo->lastInsertId();

            $itemStmt = $pdo->prepare(
                "INSERT INTO purchase_order_items 
                 (purchase_order_id, product_id, quantity_ordered, quantity_received, unit_price, discount_percent, tax_percent, subtotal)
                 VALUES (?, ?, ?, 0, ?, ?, ?, ?)"
            );

            foreach ($items as $it) {
                $subtotal = $it['quantity'] * $it['unit_price'] * (1 - ($it['discount_percent'] ?? 0) / 100);
                $itemStmt->execute([
                    $poId,
                    $it['product_id'],
                    $it['quantity'],
                    $it['unit_price'],
                    $it['discount_percent'] ?? 0.00,
                    $it['tax_percent'] ?? 0.00,
                    $subtotal
                ]);
            }

            $pdo->commit();
            return $poId;
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Update PO status (e.g. ordered, partial_received, received, cancelled)
     */
    public function updateStatus(int $id, string $status): bool
    {
        return $this->db->execute(
            "UPDATE {$this->table} SET status = ?, updated_at = NOW() WHERE id = ?",
            [$status, $id]
        );
    }

    /**
     * Get counts by status for tabs
     */
    public function getCountsByStatus(): array
    {
        $counts = [
            'all' => 0,
            'draft' => 0,
            'ordered' => 0,
            'partial_received' => 0,
            'received' => 0,
            'cancelled' => 0
        ];

        $rows = $this->db->query("SELECT status, COUNT(*) as cnt FROM {$this->table} GROUP BY status");
        foreach ($rows as $row) {
            $counts[$row['status']] = (int)$row['cnt'];
            $counts['all'] += (int)$row['cnt'];
        }

        return $counts;
    }
}
