<?php

namespace App\Models;

use Core\Model;
use Core\Database;

/**
 * CashierShift Model
 * Manages cashier drawer cash balance, shift reconciliation, and transaction aggregates
 */
class CashierShift extends Model
{
    protected string $table = 'cashier_shifts';
    protected string $primaryKey = 'id';
    protected array $fillable = [
        'user_id',
        'opening_cash',
        'closing_cash',
        'expected_cash',
        'cash_difference',
        'total_sales_amount',
        'total_transactions',
        'status',
        'opened_at',
        'closed_at',
        'notes'
    ];

    public function __construct(?Database $db = null)
    {
        parent::__construct($db);
    }

    /**
     * Get the active open shift for a cashier
     */
    public function getActiveShift(int $userId): ?array
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE user_id = ? AND status = 'open' 
                ORDER BY opened_at DESC LIMIT 1";

        return $this->db->fetch($sql, [$userId]);
    }

    /**
     * Open a new shift
     */
    public function openShift(int $userId, float $openingCash, ?string $notes = null): int
    {
        // Close any dangling open shifts first
        $this->db->execute(
            "UPDATE {$this->table} SET status = 'closed', closed_at = NOW() WHERE user_id = ? AND status = 'open'",
            [$userId]
        );

        return $this->create([
            'user_id' => $userId,
            'opening_cash' => $openingCash,
            'total_sales_amount' => 0.00,
            'total_transactions' => 0,
            'status' => 'open',
            'notes' => $notes
        ]);
    }

    /**
     * Close an active shift with cash drawer counting
     */
    public function closeShift(int $shiftId, float $closingCash, ?string $notes = null): bool
    {
        $shift = $this->find($shiftId);
        if (!$shift || $shift['status'] !== 'open') {
            return false;
        }

        // Calculate total cash collected from sales
        $salesSql = "SELECT COALESCE(SUM(total_amount), 0) as total_cash, COUNT(id) as total_count 
                     FROM sales 
                     WHERE cashier_shift_id = ? AND payment_method = 'cash' AND status = 'completed'";
        $salesSummary = $this->db->fetch($salesSql, [$shiftId]);

        $cashSales = (float)($salesSummary['total_cash'] ?? 0);
        $totalTransactions = (int)($salesSummary['total_count'] ?? 0);
        $expectedCash = (float)$shift['opening_cash'] + $cashSales;
        $difference = $closingCash - $expectedCash;

        return $this->update($shiftId, [
            'closing_cash' => $closingCash,
            'expected_cash' => $expectedCash,
            'cash_difference' => $difference,
            'total_sales_amount' => $cashSales,
            'total_transactions' => $totalTransactions,
            'status' => 'closed',
            'closed_at' => date('Y-m-d H:i:s'),
            'notes' => $notes
        ]);
    }

    /**
     * Record a sale to the active shift
     */
    public function incrementSales(int $shiftId, float $amount): bool
    {
        $sql = "UPDATE {$this->table} 
                SET total_sales_amount = total_sales_amount + ?, 
                    total_transactions = total_transactions + 1 
                WHERE id = ?";

        return $this->db->execute($sql, [$amount, $shiftId]);
    }
}
