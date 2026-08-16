<?php

namespace App\Models;

use Core\Model;
use Core\Database;

/**
 * Sale Model
 * Handles POS checkout, automated FEFO batch allocation, tax calculations, and stock deduction
 */
class Sale extends Model
{
    protected string $table = 'sales';
    protected string $primaryKey = 'id';
    protected array $fillable = [
        'invoice_number',
        'cashier_shift_id',
        'user_id',
        'customer_name',
        'customer_phone',
        'subtotal',
        'discount_amount',
        'tax_amount',
        'total_amount',
        'payment_method',
        'cash_tendered',
        'cash_change',
        'notes',
        'status'
    ];

    public function __construct(?Database $db = null)
    {
        parent::__construct($db);
    }

    /**
     * Generate unique daily invoice number
     */
    public function generateInvoiceNumber(): string
    {
        $prefix = 'INV-' . date('Ymd') . '-';
        $random = strtoupper(substr(md5(uniqid((string)mt_rand(), true)), 0, 4));
        
        $sql = "SELECT COUNT(id) as today_count FROM {$this->table} WHERE invoice_number LIKE ?";
        $res = $this->db->fetch($sql, [$prefix . '%']);
        $seq = str_pad((string)(($res['today_count'] ?? 0) + 1), 4, '0', STR_PAD_LEFT);

        return $prefix . $seq;
    }

    /**
     * Execute POS checkout with atomic transaction and automated FEFO allocation
     * 
     * @param array $saleData Master sale payload
     * @param array $items Array of cart items: [['product_id' => 1, 'quantity' => 2, 'unit_price' => 18500]]
     * @return array Result containing sale record and line items
     * @throws \Exception
     */
    public function processCheckout(array $saleData, array $items): array
    {
        if (empty($items)) {
            throw new \Exception('Cannot process checkout with an empty cart.');
        }

        $pdo = $this->db->getPdo();
        $pdo->beginTransaction();

        try {
            $invoiceNumber = $saleData['invoice_number'] ?? $this->generateInvoiceNumber();

            // 1. Calculate totals
            $subtotal = 0.00;
            foreach ($items as $item) {
                $subtotal += ((float)$item['unit_price'] * (int)$item['quantity']);
            }

            $discountAmount = (float)($saleData['discount_amount'] ?? 0.00);
            $taxRate = isset($saleData['include_tax']) && $saleData['include_tax'] ? 0.11 : 0.00;
            $taxableAmount = max(0, $subtotal - $discountAmount);
            $taxAmount = (float)($saleData['tax_amount'] ?? ($taxableAmount * $taxRate));
            $totalAmount = (float)($saleData['total_amount'] ?? ($taxableAmount + $taxAmount));

            $cashTendered = (float)($saleData['cash_tendered'] ?? $totalAmount);
            $cashChange = max(0.00, $cashTendered - $totalAmount);

            // 2. Insert master sale
            $stmt = $pdo->prepare("INSERT INTO sales (
                invoice_number, cashier_shift_id, user_id, customer_name, customer_phone,
                subtotal, discount_amount, tax_amount, total_amount, payment_method,
                cash_tendered, cash_change, notes, status
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'completed')");

            $stmt->execute([
                $invoiceNumber,
                !empty($saleData['cashier_shift_id']) ? (int)$saleData['cashier_shift_id'] : null,
                (int)$saleData['user_id'],
                !empty($saleData['customer_name']) ? trim($saleData['customer_name']) : 'General Patient (Walk-in)',
                !empty($saleData['customer_phone']) ? trim($saleData['customer_phone']) : null,
                $subtotal,
                $discountAmount,
                $taxAmount,
                $totalAmount,
                $saleData['payment_method'] ?? 'cash',
                $cashTendered,
                $cashChange,
                $saleData['notes'] ?? null
            ]);

            $saleId = (int)$pdo->lastInsertId();

            // 3. Process each item with FEFO stock allocation
            $allocatedItems = [];
            foreach ($items as $item) {
                $productId = (int)$item['product_id'];
                $requestedQty = (int)$item['quantity'];
                $unitPrice = (float)$item['unit_price'];

                // Check product availability
                $prodStmt = $pdo->prepare("SELECT id, name, sku, stock_quantity FROM products WHERE id = ? FOR UPDATE");
                $prodStmt->execute([$productId]);
                $product = $prodStmt->fetch(\PDO::FETCH_ASSOC);

                if (!$product) {
                    throw new \Exception("Product ID {$productId} does not exist.");
                }

                if ($product['stock_quantity'] < $requestedQty) {
                    throw new \Exception("Insufficient stock for '{$product['name']}'. Requested: {$requestedQty}, Available: {$product['stock_quantity']}.");
                }

                // Query active batches prioritized by FEFO (Earliest Expiry First)
                $batchStmt = $pdo->prepare("SELECT id, batch_number, expiry_date, current_quantity 
                                            FROM batches 
                                            WHERE product_id = ? AND current_quantity > 0 AND is_active = 1 
                                            ORDER BY expiry_date ASC, id ASC FOR UPDATE");
                $batchStmt->execute([$productId]);
                $batches = $batchStmt->fetchAll(\PDO::FETCH_ASSOC);

                $remainingToAllocate = $requestedQty;

                if (!empty($batches)) {
                    foreach ($batches as $batch) {
                        if ($remainingToAllocate <= 0) break;

                        $batchId = (int)$batch['id'];
                        $availableInBatch = (int)$batch['current_quantity'];
                        $allocatedQty = min($remainingToAllocate, $availableInBatch);

                        // Deduct from batch
                        $deductBatch = $pdo->prepare("UPDATE batches SET current_quantity = current_quantity - ? WHERE id = ?");
                        $deductBatch->execute([$allocatedQty, $batchId]);

                        // Record sale item line
                        $itemSubtotal = $unitPrice * $allocatedQty;
                        $insertItem = $pdo->prepare("INSERT INTO sale_items (
                            sale_id, product_id, batch_id, quantity, unit_price, subtotal, discount_amount, total_price
                        ) VALUES (?, ?, ?, ?, ?, ?, 0.00, ?)");
                        $insertItem->execute([$saleId, $productId, $batchId, $allocatedQty, $unitPrice, $itemSubtotal, $itemSubtotal]);

                        $remainingToAllocate -= $allocatedQty;

                        $allocatedItems[] = [
                            'product_name' => $product['name'],
                            'sku' => $product['sku'],
                            'batch_number' => $batch['batch_number'],
                            'quantity' => $allocatedQty,
                            'unit_price' => $unitPrice,
                            'total' => $itemSubtotal
                        ];
                    }
                }

                // If some qty remains (unbatched inventory fallback)
                if ($remainingToAllocate > 0) {
                    $itemSubtotal = $unitPrice * $remainingToAllocate;
                    $insertItem = $pdo->prepare("INSERT INTO sale_items (
                        sale_id, product_id, batch_id, quantity, unit_price, subtotal, discount_amount, total_price
                    ) VALUES (?, ?, NULL, ?, ?, ?, 0.00, ?)");
                    $insertItem->execute([$saleId, $productId, $remainingToAllocate, $unitPrice, $itemSubtotal, $itemSubtotal]);

                    $allocatedItems[] = [
                        'product_name' => $product['name'],
                        'sku' => $product['sku'],
                        'batch_number' => 'UNBATCHED',
                        'quantity' => $remainingToAllocate,
                        'unit_price' => $unitPrice,
                        'total' => $itemSubtotal
                    ];
                }

                // Deduct total product stock quantity
                $deductProd = $pdo->prepare("UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ?");
                $deductProd->execute([$requestedQty, $productId]);

                // Record stock movement ledger
                $newBalance = $product['stock_quantity'] - $requestedQty;
                $insertMovement = $pdo->prepare("INSERT INTO stock_movements (
                    product_id, type, quantity, balance_after, reference_type, reference_id, user_id, notes
                ) VALUES (?, 'sale', ?, ?, 'sale', ?, ?, ?)");
                $insertMovement->execute([
                    $productId,
                    $requestedQty,
                    $newBalance,
                    $saleId,
                    $saleData['user_id'],
                    "POS Checkout Invoice #{$invoiceNumber}"
                ]);
            }

            // 4. Update cashier shift if present
            if (!empty($saleData['cashier_shift_id'])) {
                $shiftUpdate = $pdo->prepare("UPDATE cashier_shifts SET total_sales_amount = total_sales_amount + ?, total_transactions = total_transactions + 1 WHERE id = ?");
                $shiftUpdate->execute([$totalAmount, (int)$saleData['cashier_shift_id']]);
            }

            $pdo->commit();

            return [
                'success' => true,
                'sale_id' => $saleId,
                'invoice_number' => $invoiceNumber,
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'payment_method' => $saleData['payment_method'] ?? 'cash',
                'cash_tendered' => $cashTendered,
                'cash_change' => $cashChange,
                'customer_name' => $saleData['customer_name'] ?? 'General Patient (Walk-in)',
                'created_at' => date('Y-m-d H:i:s'),
                'items' => $allocatedItems
            ];

        } catch (\Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Get sale with details and items for receipt printing
     */
    public function getWithDetails(int $id): ?array
    {
        $sql = "SELECT s.*, u.full_name as cashier_name
                FROM {$this->table} s
                LEFT JOIN users u ON s.user_id = u.id
                WHERE s.id = ? LIMIT 1";
        $sale = $this->db->fetch($sql, [$id]);

        if (!$sale) return null;

        $itemsSql = "SELECT si.*, p.name as product_name, p.sku, u.symbol as unit_symbol, b.batch_number, b.expiry_date
                     FROM sale_items si
                     JOIN products p ON si.product_id = p.id
                     LEFT JOIN units u ON p.unit_id = u.id
                     LEFT JOIN batches b ON si.batch_id = b.id
                     WHERE si.sale_id = ?
                     ORDER BY si.id ASC";
        $sale['items'] = $this->db->query($itemsSql, [$id]);

        return $sale;
    }

    /**
     * Get recent sales list for history
     */
    public function getRecentSales(int $limit = 50): array
    {
        $sql = "SELECT s.*, u.full_name as cashier_name, COUNT(si.id) as item_count
                FROM {$this->table} s
                LEFT JOIN users u ON s.user_id = u.id
                LEFT JOIN sale_items si ON s.id = si.sale_id
                GROUP BY s.id
                ORDER BY s.created_at DESC
                LIMIT ?";

        return $this->db->query($sql, [$limit]);
    }

    /**
     * Get paginated sales history with server-side filtering
     */
    public function getSalesPaginated(array $filters = [], int $page = 1, int $perPage = 25): array
    {
        $page = max(1, $page);
        $perPage = max(1, min(100, $perPage));
        $offset = ($page - 1) * $perPage;

        $where = "WHERE 1=1";
        $params = [];

        if (!empty($filters['search'])) {
            $where .= " AND (s.invoice_number LIKE ? OR s.customer_name LIKE ? OR s.customer_phone LIKE ?)";
            $term = "%{$filters['search']}%";
            $params = array_merge($params, [$term, $term, $term]);
        }

        if (!empty($filters['payment_method']) && $filters['payment_method'] !== 'all') {
            $where .= " AND s.payment_method = ?";
            $params[] = $filters['payment_method'];
        }

        if (!empty($filters['start_date'])) {
            $where .= " AND DATE(s.created_at) >= ?";
            $params[] = $filters['start_date'];
        }

        if (!empty($filters['end_date'])) {
            $where .= " AND DATE(s.created_at) <= ?";
            $params[] = $filters['end_date'];
        }

        $countSql = "SELECT COUNT(*) as total FROM {$this->table} s {$where}";
        $countRow = $this->db->fetch($countSql, $params);
        $total = $countRow ? (int)$countRow['total'] : 0;

        $dataSql = "SELECT s.*, u.full_name as cashier_name, COUNT(si.id) as item_count
                    FROM {$this->table} s
                    LEFT JOIN users u ON s.user_id = u.id
                    LEFT JOIN sale_items si ON s.id = si.sale_id
                    {$where}
                    GROUP BY s.id
                    ORDER BY s.created_at DESC
                    LIMIT {$perPage} OFFSET {$offset}";

        $items = $this->db->query($dataSql, $params);
        $totalPages = (int)ceil($total / max(1, $perPage));

        return [
            'items' => $items,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'total_pages' => $totalPages,
            'from' => $total > 0 ? $offset + 1 : 0,
            'to' => min($offset + count($items), $total)
        ];
    }
}
