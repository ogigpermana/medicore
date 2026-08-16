<?php

namespace App\Models;

use Core\Model;
use Core\Database;
use PDO;

class StockTransfer extends Model
{
    protected string $table = 'stock_transfers';
    protected string $primaryKey = 'id';

    public function __construct(?Database $db = null)
    {
        parent::__construct($db);
    }

    /**
     * Generate Transfer Number
     * e.g. TRF-20260816-0001
     */
    public function generateNumber(): string
    {
        $datePrefix = date('Ymd');
        $sql = "SELECT transfer_number FROM {$this->table} 
                WHERE transfer_number LIKE ? 
                ORDER BY id DESC LIMIT 1";
        $last = $this->db->fetch($sql, ["TRF-{$datePrefix}-%"]);

        if ($last && !empty($last['transfer_number'])) {
            $parts = explode('-', $last['transfer_number']);
            $seq = (int)end($parts) + 1;
        } else {
            $seq = 1;
        }

        return sprintf("TRF-%s-%04d", $datePrefix, $seq);
    }

    /**
     * Get list of transfers with branch metadata
     */
    public function getList(?string $status = null): array
    {
        $sql = "SELECT st.*, 
                       sb.name as source_branch_name, sb.code as source_branch_code, sb.type as source_branch_type,
                       db.name as destination_branch_name, db.code as destination_branch_code, db.type as destination_branch_type,
                       u.full_name as requester_name,
                       ap.full_name as approver_name,
                       rc.full_name as receiver_name
                FROM {$this->table} st
                JOIN branches sb ON st.source_branch_id = sb.id
                JOIN branches db ON st.destination_branch_id = db.id
                JOIN users u ON st.requested_by = u.id
                LEFT JOIN users ap ON st.approved_by = ap.id
                LEFT JOIN users rc ON st.received_by = rc.id
                WHERE 1=1";
        $params = [];

        if ($status && $status !== 'all') {
            $sql .= " AND st.status = ?";
            $params[] = $status;
        }

        $sql .= " ORDER BY st.id DESC";

        return $this->db->query($sql, $params);
    }

    /**
     * Get single transfer with items, batch lots, and branch details
     */
    public function getDetails(int $id): ?array
    {
        $sql = "SELECT st.*, 
                       sb.name as source_branch_name, sb.code as source_branch_code, sb.type as source_branch_type,
                       sb.address as source_address, sb.phone as source_phone, sb.pharmacist_in_charge as source_apj, sb.sipa_number as source_sipa,
                       db.name as destination_branch_name, db.code as destination_branch_code, db.type as destination_branch_type,
                       db.address as destination_address, db.phone as destination_phone, db.pharmacist_in_charge as destination_apj, db.sipa_number as destination_sipa,
                       u.full_name as requester_name,
                       ap.full_name as approver_name,
                       rc.full_name as receiver_name
                FROM {$this->table} st
                JOIN branches sb ON st.source_branch_id = sb.id
                JOIN branches db ON st.destination_branch_id = db.id
                JOIN users u ON st.requested_by = u.id
                LEFT JOIN users ap ON st.approved_by = ap.id
                LEFT JOIN users rc ON st.received_by = rc.id
                WHERE st.id = ?";
        $st = $this->db->fetch($sql, [$id]);

        if (!$st) {
            return null;
        }

        $itemSql = "SELECT sti.*, 
                           p.name as product_name, p.sku, p.barcode, u.symbol as unit_symbol,
                           c.name as category_name, b.batch_number, b.expiry_date
                    FROM stock_transfer_items sti
                    JOIN products p ON sti.product_id = p.id
                    LEFT JOIN units u ON p.unit_id = u.id
                    LEFT JOIN categories c ON p.category_id = c.id
                    LEFT JOIN batches b ON sti.batch_id = b.id
                    WHERE sti.stock_transfer_id = ?
                    ORDER BY p.name ASC";
        $st['items'] = $this->db->query($itemSql, [$id]);

        return $st;
    }

    /**
     * Create a new Stock Transfer (Draft or Pending Approval)
     */
    public function createTransfer(array $data, array $items): int
    {
        $pdo = $this->db->connect();
        $pdo->beginTransaction();

        try {
            $transferNumber = $this->generateNumber();
            $sourceId = (int)$data['source_branch_id'];
            $destId = (int)$data['destination_branch_id'];
            $requestedBy = (int)$data['requested_by'];
            $status = $data['status'] ?? 'pending_approval';
            $shippingNotes = $data['shipping_notes'] ?? null;

            $totalItems = count($items);
            $totalQty = 0;
            foreach ($items as $it) {
                $totalQty += (int)$it['qty_requested'];
            }

            $stmt = $pdo->prepare(
                "INSERT INTO {$this->table}
                 (transfer_number, source_branch_id, destination_branch_id, status, requested_by, total_items, total_qty_sent, shipping_notes, created_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())"
            );
            $stmt->execute([
                $transferNumber,
                $sourceId,
                $destId,
                $status,
                $requestedBy,
                $totalItems,
                $totalQty,
                $shippingNotes
            ]);
            $transferId = (int)$pdo->lastInsertId();

            $itemStmt = $pdo->prepare(
                "INSERT INTO stock_transfer_items
                 (stock_transfer_id, product_id, batch_id, qty_requested, qty_sent, unit_buy_price, notes, created_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?, NOW())"
            );

            foreach ($items as $it) {
                $prodId = (int)$it['product_id'];
                $batchId = !empty($it['batch_id']) ? (int)$it['batch_id'] : null;
                $qty = (int)$it['qty_requested'];
                $buyPrice = (float)($it['unit_buy_price'] ?? 0.0);
                $notes = $it['notes'] ?? null;

                $itemStmt->execute([
                    $transferId,
                    $prodId,
                    $batchId,
                    $qty,
                    $qty, // Sent matches requested initially
                    $buyPrice,
                    $notes
                ]);
            }

            $pdo->commit();
            return $transferId;
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Dispatch Transfer (Kirim Mutasi Stok / Surat Jalan):
     * Deducts stock from source branch and marks status as 'in_transit'
     */
    public function dispatchTransfer(int $transferId, int $approverId, string $driverName, string $vehicleNumber, ?string $shippingNotes = null): bool
    {
        $pdo = $this->db->connect();
        $pdo->beginTransaction();

        try {
            $st = $this->getDetails($transferId);
            if (!$st || !in_array($st['status'], ['draft', 'pending_approval'])) {
                $pdo->rollBack();
                return false;
            }

            $updateProdStmt = $pdo->prepare("UPDATE products SET stock_quantity = GREATEST(0, stock_quantity - ?) WHERE id = ?");
            $updateBatchStmt = $pdo->prepare("UPDATE batches SET current_quantity = GREATEST(0, current_quantity - ?) WHERE id = ?");
            $movementStmt = $pdo->prepare(
                "INSERT INTO stock_movements 
                 (product_id, batch_id, type, quantity, balance_after, reference_type, reference_id, notes, user_id)
                 VALUES (?, ?, 'out', ?, ?, 'stock_transfer', ?, ?, ?)"
            );

            foreach ($st['items'] as $it) {
                $prodId = (int)$it['product_id'];
                $batchId = !empty($it['batch_id']) ? (int)$it['batch_id'] : null;
                $qtySent = (int)$it['qty_sent'];

                // Deduct master stock
                $prodRow = $this->db->fetch("SELECT stock_quantity FROM products WHERE id = ?", [$prodId]);
                $currStock = (int)($prodRow['stock_quantity'] ?? 0);
                $balanceAfter = max(0, $currStock - $qtySent);

                $updateProdStmt->execute([$qtySent, $prodId]);

                if ($batchId) {
                    $updateBatchStmt->execute([$qtySent, $batchId]);
                }

                // Log movement
                $movNotes = "Mutasi Keluar ke {$st['destination_branch_name']} ({$st['transfer_number']}) [Driver: {$driverName}]";
                $movementStmt->execute([
                    $prodId,
                    $batchId,
                    $qtySent,
                    $balanceAfter,
                    $transferId,
                    $movNotes,
                    $approverId
                ]);
            }

            // Update transfer header
            $upStmt = $pdo->prepare(
                "UPDATE {$this->table}
                 SET status = 'in_transit', approved_by = ?, driver_name = ?, vehicle_number = ?, shipping_notes = COALESCE(?, shipping_notes), departure_date = NOW(), updated_at = NOW()
                 WHERE id = ?"
            );
            $upStmt->execute([$approverId, $driverName, $vehicleNumber, $shippingNotes, $transferId]);

            $pdo->commit();
            return true;
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Receive Transfer at Destination Branch:
     * Verifies received quantities and marks status as 'received'
     */
    public function receiveTransfer(int $transferId, int $receiverId, array $receivedItems): bool
    {
        $pdo = $this->db->connect();
        $pdo->beginTransaction();

        try {
            $st = $this->getDetails($transferId);
            if (!$st || $st['status'] !== 'in_transit') {
                $pdo->rollBack();
                return false;
            }

            $updateItemStmt = $pdo->prepare(
                "UPDATE stock_transfer_items 
                 SET qty_received = ?, notes = COALESCE(?, notes), updated_at = NOW() 
                 WHERE id = ? AND stock_transfer_id = ?"
            );

            $updateProdStmt = $pdo->prepare("UPDATE products SET stock_quantity = stock_quantity + ? WHERE id = ?");
            $updateBatchStmt = $pdo->prepare("UPDATE batches SET current_quantity = current_quantity + ? WHERE id = ?");
            $movementStmt = $pdo->prepare(
                "INSERT INTO stock_movements 
                 (product_id, batch_id, type, quantity, balance_after, reference_type, reference_id, notes, user_id)
                 VALUES (?, ?, 'in', ?, ?, 'stock_transfer', ?, ?, ?)"
            );

            $totalReceived = 0;

            foreach ($receivedItems as $it) {
                $itemId = (int)$it['id'];
                $qtyRec = (int)$it['qty_received'];
                $notes = $it['notes'] ?? null;

                $updateItemStmt->execute([$qtyRec, $notes, $itemId, $transferId]);
                $totalReceived += $qtyRec;

                // Find original item details
                $origItem = null;
                foreach ($st['items'] as $orig) {
                    if ((int)$orig['id'] === $itemId) {
                        $origItem = $orig;
                        break;
                    }
                }

                if ($origItem && $qtyRec > 0) {
                    $prodId = (int)$origItem['product_id'];
                    $batchId = !empty($origItem['batch_id']) ? (int)$origItem['batch_id'] : null;

                    $prodRow = $this->db->fetch("SELECT stock_quantity FROM products WHERE id = ?", [$prodId]);
                    $currStock = (int)($prodRow['stock_quantity'] ?? 0);
                    $balanceAfter = $currStock + $qtyRec;

                    $updateProdStmt->execute([$qtyRec, $prodId]);
                    if ($batchId) {
                        $updateBatchStmt->execute([$qtyRec, $batchId]);
                    }

                    $movNotes = "Penerimaan Mutasi dari {$st['source_branch_name']} ({$st['transfer_number']})";
                    $movementStmt->execute([
                        $prodId,
                        $batchId,
                        $qtyRec,
                        $balanceAfter,
                        $transferId,
                        $movNotes,
                        $receiverId
                    ]);
                }
            }

            // Update parent transfer status to received
            $upParentStmt = $pdo->prepare(
                "UPDATE {$this->table}
                 SET status = 'received', received_by = ?, total_qty_received = ?, received_date = NOW(), updated_at = NOW()
                 WHERE id = ?"
            );
            $upParentStmt->execute([$receiverId, $totalReceived, $transferId]);

            $pdo->commit();
            return true;
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}
