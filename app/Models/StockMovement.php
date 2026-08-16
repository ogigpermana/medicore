<?php

namespace App\Models;

use Core\Model;
use Core\Database;

/**
 * Stock Movement Model
 * Handles immutable stock ledger logging
 */
class StockMovement extends Model
{
    protected string $table = 'stock_movements';
    protected string $primaryKey = 'id';
    protected array $fillable = [
        'product_id',
        'batch_id',
        'type',
        'quantity',
        'balance_after',
        'reference_type',
        'reference_id',
        'user_id',
        'notes'
    ];

    public function __construct(?Database $db = null)
    {
        parent::__construct($db);
    }

    /**
     * Log a stock movement transaction
     */
    public function logMovement(array $data): int
    {
        return $this->create($data);
    }
}
