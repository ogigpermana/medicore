<?php

namespace App\Models;

use Core\Model;
use Core\Database;

class Branch extends Model
{
    protected string $table = 'branches';
    protected string $primaryKey = 'id';

    public function __construct(?Database $db = null)
    {
        parent::__construct($db);
    }

    /**
     * Get all active branches
     */
    public function getActive(): array
    {
        return $this->db->query("SELECT * FROM {$this->table} WHERE is_active = 1 ORDER BY type ASC, name ASC");
    }

    /**
     * Find branch by code
     */
    public function findByCode(string $code): ?array
    {
        return $this->db->fetch("SELECT * FROM {$this->table} WHERE code = ? LIMIT 1", [$code]);
    }
}
