<?php

namespace App\Models;

use Core\Model;
use Core\Database;

/**
 * Batch Model
 * Handles First-Expired, First-Out (FEFO) batch priority, expiration sentinel, and lot tracking
 */
class Batch extends Model
{
    protected string $table = 'batches';
    protected string $primaryKey = 'id';
    protected array $fillable = [
        'product_id',
        'batch_number',
        'expiry_date',
        'manufacture_date',
        'initial_quantity',
        'current_quantity',
        'buy_price',
        'supplier_id',
        'received_date',
        'is_active'
    ];

    public function __construct(?Database $db = null)
    {
        parent::__construct($db);
    }

    /**
     * Get active batches for a product sorted strictly by FEFO (Earliest Expiry First)
     */
    public function getFefoBatches(int $productId): array
    {
        $sql = "SELECT b.*, s.name as supplier_name,
                       DATEDIFF(b.expiry_date, CURDATE()) as days_until_expiry
                FROM {$this->table} b
                LEFT JOIN suppliers s ON b.supplier_id = s.id
                WHERE b.product_id = ? AND b.current_quantity > 0 AND b.is_active = 1
                ORDER BY b.expiry_date ASC, b.received_date ASC";

        return $this->db->query($sql, [$productId]);
    }

    /**
     * Get all expiring batches across all products within given days threshold
     */
    public function getExpiringBatches(int $daysThreshold = 60): array
    {
        $sql = "SELECT b.*, p.name as product_name, p.sku as product_sku, u.symbol as unit_symbol,
                       s.name as supplier_name,
                       DATEDIFF(b.expiry_date, CURDATE()) as days_until_expiry
                FROM {$this->table} b
                JOIN products p ON b.product_id = p.id
                LEFT JOIN units u ON p.unit_id = u.id
                LEFT JOIN suppliers s ON b.supplier_id = s.id
                WHERE b.current_quantity > 0 
                  AND b.is_active = 1 
                  AND b.expiry_date <= DATE_ADD(CURDATE(), INTERVAL ? DAY)
                ORDER BY b.expiry_date ASC";

        return $this->db->query($sql, [$daysThreshold]);
    }

    /**
     * Deduct stock quantity from a specific batch
     */
    public function deductQuantity(int $batchId, int $quantity): bool
    {
        $sql = "UPDATE {$this->table} 
                SET current_quantity = current_quantity - ? 
                WHERE id = ? AND current_quantity >= ?";

        return $this->db->execute($sql, [$quantity, $batchId, $quantity]);
    }
}
