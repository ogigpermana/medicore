<?php

namespace Core;

/**
 * Model Class
 * Base model with database operations
 */

abstract class Model
{
    protected Database $db;
    protected string $table;
    protected string $primaryKey = 'id';
    protected array $fillable = [];

    public function __construct(?Database $db = null)
    {
        $this->db = $db ?? app()->getContainer()->get(Database::class);
    }

    /**
     * Set database connection (for testing)
     */
    public function setDatabase(Database $db): void
    {
        $this->db = $db;
    }

    /**
     * Get database connection
     */
    public function getDatabase(): Database
    {
        return $this->db;
    }

    /**
     * Find a record by ID
     */
    public function find(int $id): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ? LIMIT 1";
        return $this->db->fetch($sql, [$id]);
    }

    /**
     * Get all records
     */
    public function all(): array
    {
        $sql = "SELECT * FROM {$this->table}";
        return $this->db->query($sql);
    }

    /**
     * Add where clause
     */
    public function where(string $column, string $operator, mixed $value): self
    {
        $this->whereConditions[] = [$column, $operator, $value];
        return $this;
    }

    /**
     * Get records with where conditions
     */
    public function get(): array
    {
        $sql = "SELECT * FROM {$this->table}";
        $params = [];

        if (!empty($this->whereConditions)) {
            $whereParts = [];
            foreach ($this->whereConditions as $condition) {
                $whereParts[] = "{$condition[0]} {$condition[1]} ?";
                $params[] = $condition[2];
            }
            $sql .= " WHERE " . implode(' AND ', $whereParts);
        }

        $this->whereConditions = []; // Reset
        return $this->db->query($sql, $params);
    }

    /**
     * Get first matching record
     */
    public function first(): ?array
    {
        $results = $this->get();
        return $results[0] ?? null;
    }

    /**
     * Create a new record
     */
    public function create(array $data): int
    {
        $filteredData = $this->filterFillable($data);
        return $this->db->insert($this->table, $filteredData);
    }

    /**
     * Update a record
     */
    public function update(int $id, array $data): bool
    {
        $filteredData = $this->filterFillable($data);
        return $this->db->update($this->table, $filteredData, "{$this->primaryKey} = ?", [$id]);
    }

    /**
     * Delete a record
     */
    public function delete(int $id): bool
    {
        return $this->db->delete($this->table, "{$this->primaryKey} = ?", [$id]);
    }

    /**
     * Paginate records
     */
    public function paginate(int $page = 1, int $perPage = 15): array
    {
        $offset = ($page - 1) * $perPage;

        $sql = "SELECT * FROM {$this->table} LIMIT {$perPage} OFFSET {$offset}";
        $data = $this->db->query($sql);

        $countSql = "SELECT COUNT(*) as total FROM {$this->table}";
        $total = $this->db->fetch($countSql)['total'];

        return [
            'data' => $data,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'last_page' => ceil($total / $perPage)
            ]
        ];
    }

    /**
     * Filter data by fillable fields
     */
    private function filterFillable(array $data): array
    {
        if (empty($this->fillable)) {
            return $data;
        }

        return array_intersect_key($data, array_flip($this->fillable));
    }

    private array $whereConditions = [];
}