<?php

namespace App\Models;

use Core\Model;
use Core\Database;

/**
 * Category Model
 * Handles drug and item categories
 */
class Category extends Model
{
    protected string $table = 'categories';
    protected string $primaryKey = 'id';
    protected array $fillable = [
        'name',
        'slug',
        'description',
        'requires_prescription',
        'is_active'
    ];

    public function __construct(?Database $db = null)
    {
        parent::__construct($db);
    }

    /**
     * Get all active categories with product counts
     */
    public function getActiveWithCounts(): array
    {
        $sql = "SELECT c.*, COUNT(p.id) as product_count
                FROM {$this->table} c
                LEFT JOIN products p ON c.id = p.category_id AND p.is_active = 1
                WHERE c.is_active = 1
                GROUP BY c.id
                ORDER BY c.name ASC";

        return $this->db->query($sql);
    }

    /**
     * Find category by slug
     */
    public function findBySlug(string $slug): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE slug = ? LIMIT 1";
        return $this->db->fetch($sql, [$slug]);
    }

    /**
     * Generate unique slug from category name
     */
    public function generateSlug(string $name, ?int $ignoreId = null): string
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-'));
        if (empty($slug)) $slug = 'category';

        $baseSlug = $slug;
        $counter = 1;

        while (true) {
            $sql = "SELECT id FROM {$this->table} WHERE slug = ?";
            $params = [$slug];

            if ($ignoreId !== null) {
                $sql .= " AND id != ?";
                $params[] = $ignoreId;
            }

            $res = $this->db->fetch($sql, $params);
            if (!$res) break;

            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Check if category can be safely deleted (no products attached)
     */
    public function canDelete(int $id): bool
    {
        $sql = "SELECT COUNT(id) as count FROM products WHERE category_id = ?";
        $res = $this->db->fetch($sql, [$id]);
        return (int)($res['count'] ?? 0) === 0;
    }
}
