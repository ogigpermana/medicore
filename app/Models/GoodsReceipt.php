<?php

namespace App\Models;

use Core\Model;
use Core\Database;
use PDO;

class GoodsReceipt extends Model
{
    protected string $table = 'goods_receipts';
    protected string $primaryKey = 'id';

    public function __construct(?Database $db = null)
    {
        parent::__construct($db);
    }

    /**
     * Generate GRN Number (Goods Receipt Note)
     */
    public function generateGrnNumber(): string
    {
        $datePrefix = date('Ymd');
        $sql = "SELECT grn_number FROM {$this->table} 
                WHERE grn_number LIKE ? 
                ORDER BY id DESC LIMIT 1";
        $last = $this->db->fetch($sql, ["GRN-{$datePrefix}-%"]);

        if ($last && !empty($last['grn_number'])) {
            $parts = explode('-', $last['grn_number']);
            $seq = (int)end($parts) + 1;
        } else {
            $seq = 1;
        }

        return sprintf("GRN-%s-%04d", $datePrefix, $seq);
    }

    /**
     * Get Goods Receipts list (or Accounts Payable list)
     */
    public function getList(?string $paymentStatus = null, bool $overdueOnly = false): array
    {
        $sql = "SELECT gr.*, s.name as supplier_name, s.code as supplier_code, s.phone as supplier_phone,
                       po.po_number, po.sp_type, u.full_name as receiver_name,
                       DATEDIFF(gr.due_date, CURDATE()) as days_until_due,
                       (SELECT COUNT(*) FROM goods_receipt_items WHERE goods_receipt_id = gr.id) as item_count
                FROM {$this->table} gr
                JOIN suppliers s ON gr.supplier_id = s.id
                JOIN users u ON gr.received_by = u.id
                LEFT JOIN purchase_orders po ON gr.purchase_order_id = po.id
                WHERE 1=1";
        $params = [];

        if ($paymentStatus && $paymentStatus !== 'all') {
            $sql .= " AND gr.payment_status = ?";
            $params[] = $paymentStatus;
        }

        if ($overdueOnly) {
            $sql .= " AND gr.due_date < CURDATE() AND gr.payment_status != 'paid'";
        }

        $sql .= " ORDER BY gr.id DESC";

        return $this->db->query($sql, $params);
    }

    /**
     * Get single Goods Receipt details with items and payment history
     */
    public function getDetails(int $id): ?array
    {
        $sql = "SELECT gr.*, s.name as supplier_name, s.code as supplier_code, s.phone as supplier_phone,
                       s.email as supplier_email, s.address as supplier_address, s.contact_person,
                       po.po_number, po.sp_type, po.order_date,
                       u.full_name as receiver_name
                FROM {$this->table} gr
                JOIN suppliers s ON gr.supplier_id = s.id
                JOIN users u ON gr.received_by = u.id
                LEFT JOIN purchase_orders po ON gr.purchase_order_id = po.id
                WHERE gr.id = ?";
        $gr = $this->db->fetch($sql, [$id]);

        if (!$gr) {
            return null;
        }

        // Fetch received items & batch info
        $itemSql = "SELECT gri.*, p.name as product_name, p.sku, p.barcode, u.symbol as unit_symbol,
                           b.current_quantity as batch_current_qty
                    FROM goods_receipt_items gri
                    JOIN products p ON gri.product_id = p.id
                    LEFT JOIN units u ON p.unit_id = u.id
                    LEFT JOIN batches b ON gri.batch_id = b.id
                    WHERE gri.goods_receipt_id = ?
                    ORDER BY gri.id ASC";
        $gr['items'] = $this->db->query($itemSql, [$id]);

        // Fetch payments made towards this invoice
        $paySql = "SELECT ap.*, u.full_name as payer_name
                   FROM ap_payments ap
                   JOIN users u ON ap.created_by = u.id
                   WHERE ap.goods_receipt_id = ?
                   ORDER BY ap.id DESC";
        $gr['payments'] = $this->db->query($paySql, [$id]);

        return $gr;
    }

    /**
     * Receive Goods, Create Batches in FEFO Inventory, Update PO Status & Log AP Invoice
     */
    public function receiveGoods(array $grData, array $items): int
    {
        $pdo = $this->db->connect();
        $pdo->beginTransaction();

        try {
            $grnNumber = $grData['grn_number'] ?? $this->generateGrnNumber();

            // Insert goods_receipts record
            $stmt = $pdo->prepare(
                "INSERT INTO {$this->table} 
                 (grn_number, purchase_order_id, supplier_id, received_by, invoice_number, invoice_date, 
                  due_date, subtotal, tax_amount, total_amount, amount_paid, payment_status, notes)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0.00, 'unpaid', ?)"
            );

            $stmt->execute([
                $grnNumber,
                $grData['purchase_order_id'] ?? null,
                $grData['supplier_id'],
                $grData['received_by'],
                $grData['invoice_number'],
                $grData['invoice_date'] ?? date('Y-m-d'),
                $grData['due_date'] ?? date('Y-m-d', strtotime('+30 days')),
                $grData['subtotal'] ?? 0.00,
                $grData['tax_amount'] ?? 0.00,
                $grData['total_amount'],
                $grData['notes'] ?? null
            ]);

            $grId = (int)$pdo->lastInsertId();

            $batchStmt = $pdo->prepare(
                "INSERT INTO batches 
                 (product_id, batch_number, expiry_date, initial_quantity, current_quantity, buy_price, supplier_id, received_date, is_active)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)"
            );

            $stockUpdateStmt = $pdo->prepare(
                "UPDATE products SET stock_quantity = stock_quantity + ? WHERE id = ?"
            );

            $stockMoveStmt = $pdo->prepare(
                "INSERT INTO stock_movements 
                 (product_id, batch_id, type, quantity, balance_after, reference_type, reference_id, notes, user_id)
                 VALUES (?, ?, 'in', ?, ?, 'purchase', ?, ?, ?)"
            );

            $grItemStmt = $pdo->prepare(
                "INSERT INTO goods_receipt_items 
                 (goods_receipt_id, product_id, batch_number, expiry_date, quantity_received, buy_price, subtotal, batch_id)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
            );

            $poItemUpdateStmt = $pdo->prepare(
                "UPDATE purchase_order_items 
                 SET quantity_received = quantity_received + ?
                 WHERE purchase_order_id = ? AND product_id = ?"
            );

            $getCurrentStockStmt = $pdo->prepare("SELECT stock_quantity FROM products WHERE id = ?");

            foreach ($items as $it) {
                $qty = (int)$it['quantity_received'];
                if ($qty <= 0) continue;

                // 1. Create or replenish batch
                $batchStmt->execute([
                    $it['product_id'],
                    $it['batch_number'],
                    $it['expiry_date'],
                    $qty,
                    $qty,
                    $it['buy_price'],
                    $grData['supplier_id'],
                    $grData['invoice_date'] ?? date('Y-m-d')
                ]);
                $batchId = (int)$pdo->lastInsertId();

                // 2. Query current stock and calculate balance after
                $getCurrentStockStmt->execute([$it['product_id']]);
                $currentStock = (int)$getCurrentStockStmt->fetchColumn();
                $newBalance = $currentStock + $qty;

                // 3. Increment product's master stock
                $stockUpdateStmt->execute([$qty, $it['product_id']]);

                // 4. Log stock movement
                $stockMoveStmt->execute([
                    $it['product_id'],
                    $batchId,
                    $qty,
                    $newBalance,
                    $grId,
                    "PBF Receiving GRN: {$grnNumber} (Inv: {$grData['invoice_number']})",
                    $grData['received_by']
                ]);

                // 4. Insert into goods_receipt_items
                $subtotal = $qty * $it['buy_price'];
                $grItemStmt->execute([
                    $grId,
                    $it['product_id'],
                    $it['batch_number'],
                    $it['expiry_date'],
                    $qty,
                    $it['buy_price'],
                    $subtotal,
                    $batchId
                ]);

                // 5. If linked to a PO, increment quantity_received in PO items
                if (!empty($grData['purchase_order_id'])) {
                    $poItemUpdateStmt->execute([$qty, $grData['purchase_order_id'], $it['product_id']]);
                }
            }

            // Update parent PO status if linked
            if (!empty($grData['purchase_order_id'])) {
                $poId = (int)$grData['purchase_order_id'];
                
                // Check if all PO items are fully received
                $checkStmt = $pdo->prepare(
                    "SELECT SUM(quantity_ordered) as total_ord, SUM(quantity_received) as total_rec 
                     FROM purchase_order_items 
                     WHERE purchase_order_id = ?"
                );
                $checkStmt->execute([$poId]);
                $totals = $checkStmt->fetch(PDO::FETCH_ASSOC);

                if ($totals && $totals['total_rec'] >= $totals['total_ord']) {
                    $newPoStatus = 'received';
                } else {
                    $newPoStatus = 'partial_received';
                }

                $pdo->prepare("UPDATE purchase_orders SET status = ?, updated_at = NOW() WHERE id = ?")
                    ->execute([$newPoStatus, $poId]);
            }

            $pdo->commit();
            return $grId;
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Record payment for PBF Invoice in Accounts Payable ledger
     */
    public function recordPayment(int $grId, float $amount, string $paymentMethod, ?string $refNumber, ?string $notes, int $userId): bool
    {
        $pdo = $this->db->connect();
        $pdo->beginTransaction();

        try {
            $stmt = $pdo->prepare("SELECT total_amount, amount_paid FROM {$this->table} WHERE id = ?");
            $stmt->execute([$grId]);
            $gr = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$gr) {
                $pdo->rollBack();
                return false;
            }

            $newPaid = (float)$gr['amount_paid'] + $amount;
            $total = (float)$gr['total_amount'];

            if ($newPaid >= $total) {
                $paymentStatus = 'paid';
            } elseif ($newPaid > 0) {
                $paymentStatus = 'partial';
            } else {
                $paymentStatus = 'unpaid';
            }

            // Insert payment record
            $payStmt = $pdo->prepare(
                "INSERT INTO ap_payments (goods_receipt_id, payment_date, amount_paid, payment_method, reference_number, notes, created_by)
                 VALUES (?, CURDATE(), ?, ?, ?, ?, ?)"
            );
            $payStmt->execute([$grId, $amount, $paymentMethod, $refNumber, $notes, $userId]);

            // Update goods receipt status
            $upStmt = $pdo->prepare(
                "UPDATE {$this->table} SET amount_paid = ?, payment_status = ?, updated_at = NOW() WHERE id = ?"
            );
            $upStmt->execute([$newPaid, $paymentStatus, $grId]);

            $pdo->commit();
            return true;
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Get Accounts Payable Summary (Total Hutang, Belum Bayar, Jatuh Tempo)
     */
    public function getApSummary(): array
    {
        $sql = "SELECT 
                    COALESCE(SUM(total_amount), 0) as total_invoiced,
                    COALESCE(SUM(amount_paid), 0) as total_paid,
                    COALESCE(SUM(total_amount - amount_paid), 0) as total_outstanding,
                    COALESCE(SUM(CASE WHEN due_date < CURDATE() AND payment_status != 'paid' THEN (total_amount - amount_paid) ELSE 0 END), 0) as total_overdue,
                    COUNT(CASE WHEN payment_status != 'paid' THEN 1 END) as pending_invoices_count,
                    COUNT(CASE WHEN due_date < CURDATE() AND payment_status != 'paid' THEN 1 END) as overdue_invoices_count
                FROM {$this->table}";

        return $this->db->fetch($sql) ?: [
            'total_invoiced' => 0,
            'total_paid' => 0,
            'total_outstanding' => 0,
            'total_overdue' => 0,
            'pending_invoices_count' => 0,
            'overdue_invoices_count' => 0
        ];
    }
}
