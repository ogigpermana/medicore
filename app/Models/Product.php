<?php

namespace App\Models;

use Core\Model;
use Core\Database;

/**
 * Product Model
 * Handles medication catalog, SKU/barcode lookup, pricing, and stock status
 */
class Product extends Model
{
    protected string $table = 'products';
    protected string $primaryKey = 'id';
    protected array $fillable = [
        'sku',
        'barcode',
        'name',
        'generic_name',
        'category_id',
        'unit_id',
        'dosage',
        'manufacturer',
        'buy_price',
        'sell_price',
        'min_stock',
        'stock_quantity',
        'requires_prescription',
        'side_effects',
        'is_active'
    ];

    public function __construct(?Database $db = null)
    {
        parent::__construct($db);
    }

    /**
     * Find product by SKU
     */
    public function findBySku(string $sku): ?array
    {
        $sql = "SELECT p.*, c.name as category_name, u.name as unit_name, u.symbol as unit_symbol
                FROM {$this->table} p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN units u ON p.unit_id = u.id
                WHERE p.sku = ? LIMIT 1";

        return $this->db->fetch($sql, [$sku]);
    }

    /**
     * Find product by barcode
     */
    public function findByBarcode(string $barcode): ?array
    {
        $sql = "SELECT p.*, c.name as category_name, u.name as unit_name, u.symbol as unit_symbol
                FROM {$this->table} p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN units u ON p.unit_id = u.id
                WHERE p.barcode = ? LIMIT 1";

        return $this->db->fetch($sql, [$barcode]);
    }

    /**
     * Get detailed product with category & unit
     */
    public function findWithDetails(int $id): ?array
    {
        $sql = "SELECT p.*, c.name as category_name, u.name as unit_name, u.symbol as unit_symbol
                FROM {$this->table} p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN units u ON p.unit_id = u.id
                WHERE p.id = ? LIMIT 1";

        return $this->db->fetch($sql, [$id]);
    }

    /**
     * Get full catalog with category, unit, and nearest expiry date
     */
    public function getCatalog(array $filters = []): array
    {
        $sql = "SELECT p.*, c.name as category_name, u.name as unit_name, u.symbol as unit_symbol,
                       MIN(b.expiry_date) as nearest_expiry,
                       COUNT(b.id) as batch_count
                FROM {$this->table} p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN units u ON p.unit_id = u.id
                LEFT JOIN batches b ON p.id = b.product_id AND b.current_quantity > 0 AND b.is_active = 1
                WHERE p.is_active = 1";

        $params = [];

        if (!empty($filters['search'])) {
            $sql .= " AND (p.name LIKE ? OR p.generic_name LIKE ? OR p.sku LIKE ? OR p.barcode LIKE ?)";
            $searchTerm = "%{$filters['search']}%";
            $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
        }

        if (!empty($filters['category_id'])) {
            $sql .= " AND p.category_id = ?";
            $params[] = $filters['category_id'];
        }

        if (isset($filters['low_stock']) && $filters['low_stock'] === true) {
            $sql .= " AND p.stock_quantity <= p.min_stock";
        }

        $sql .= " GROUP BY p.id ORDER BY p.name ASC";

        return $this->db->query($sql, $params);
    }

    /**
     * Get paginated catalog with high-performance server-side offset
     */
    public function getCatalogPaginated(array $filters = [], int $page = 1, int $perPage = 25): array
    {
        $page = max(1, $page);
        $perPage = max(1, min(100, $perPage));
        $offset = ($page - 1) * $perPage;

        // Build WHERE clause
        $where = "WHERE p.is_active = 1";
        $params = [];

        if (!empty($filters['search'])) {
            $where .= " AND (p.name LIKE ? OR p.generic_name LIKE ? OR p.sku LIKE ? OR p.barcode LIKE ?)";
            $searchTerm = "%{$filters['search']}%";
            $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
        }

        if (!empty($filters['category_id'])) {
            $where .= " AND p.category_id = ?";
            $params[] = $filters['category_id'];
        }

        if (isset($filters['low_stock']) && $filters['low_stock'] === true) {
            $where .= " AND p.stock_quantity <= p.min_stock";
        }

        // Count total matching items
        $countSql = "SELECT COUNT(*) as total FROM {$this->table} p {$where}";
        $countRow = $this->db->fetch($countSql, $params);
        $total = $countRow ? (int)$countRow['total'] : 0;

        // Fetch paginated slice with batch expiry info
        $dataSql = "SELECT p.*, c.name as category_name, u.name as unit_name, u.symbol as unit_symbol,
                           MIN(b.expiry_date) as nearest_expiry,
                           COUNT(b.id) as batch_count
                    FROM {$this->table} p
                    LEFT JOIN categories c ON p.category_id = c.id
                    LEFT JOIN units u ON p.unit_id = u.id
                    LEFT JOIN batches b ON p.id = b.product_id AND b.current_quantity > 0 AND b.is_active = 1
                    {$where}
                    GROUP BY p.id 
                    ORDER BY p.name ASC 
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

    /**
     * Get low stock items
     */
    public function getLowStock(): array
    {
        $sql = "SELECT p.*, c.name as category_name, u.symbol as unit_symbol
                FROM {$this->table} p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN units u ON p.unit_id = u.id
                WHERE p.is_active = 1 AND p.stock_quantity <= p.min_stock
                ORDER BY (p.stock_quantity - p.min_stock) ASC";

        return $this->db->query($sql);
    }

    /**
     * Update product stock quantity
     */
    public function updateStock(int $productId, int $quantityChange): bool
    {
        $sql = "UPDATE {$this->table} SET stock_quantity = stock_quantity + ? WHERE id = ?";
        return $this->db->execute($sql, [$quantityChange, $productId]);
    }
}
