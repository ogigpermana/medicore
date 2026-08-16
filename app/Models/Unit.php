<?php

namespace App\Models;

use Core\Model;
use Core\Database;

/**
 * Unit Model
 * Handles medication packaging units
 */
class Unit extends Model
{
    protected string $table = 'units';
    protected string $primaryKey = 'id';
    protected array $fillable = [
        'name',
        'symbol'
    ];

    public function __construct(?Database $db = null)
    {
        parent::__construct($db);
    }
}
