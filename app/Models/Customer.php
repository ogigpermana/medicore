<?php

namespace App\Models;

use Core\Model;
use Core\Database;

/**
 * Customer Model
 * Handles patient records, clinical allergy tracking, chronic condition notes, and CRM spend metrics
 */
class Customer extends Model
{
    protected string $table = 'customers';
    protected string $primaryKey = 'id';
    protected array $fillable = [
        'code',
        'name',
        'phone',
        'email',
        'gender',
        'birth_date',
        'address',
        'allergy_notes',
        'chronic_disease_notes',
        'total_spend',
        'total_visits',
        'last_visit_at',
        'is_active'
    ];

    public function __construct(?Database $db = null)
    {
        parent::__construct($db);
    }

    /**
     * Generate sequential patient / customer code (e.g. CUST-0001)
     */
    public function generateCode(): string
    {
        $row = $this->db->fetch("SELECT id FROM {$this->table} ORDER BY id DESC LIMIT 1");
        $nextId = ($row ? (int)$row['id'] : 0) + 1;
        return sprintf("CUST-%04d", $nextId);
    }

    /**
     * Get list of customers with search and filters
     */
    public function getCustomersList(?string $search = null, ?string $allergyOnly = 'all', ?string $status = 'all'): array
    {
        $sql = "SELECT c.*,
                       COUNT(DISTINCT s.id) as real_sales_count,
                       COUNT(DISTINCT rx.id) as real_prescription_count
                FROM {$this->table} c
                LEFT JOIN sales s ON s.customer_phone = c.phone OR s.customer_name = c.name
                LEFT JOIN prescriptions rx ON rx.patient_name = c.name
                WHERE 1=1";
        $params = [];

        if ($status === 'active') {
            $sql .= " AND c.is_active = 1";
        } elseif ($status === 'inactive') {
            $sql .= " AND c.is_active = 0";
        }

        if ($allergyOnly === 'allergy') {
            $sql .= " AND c.allergy_notes IS NOT NULL AND TRIM(c.allergy_notes) != ''";
        } elseif ($allergyOnly === 'chronic') {
            $sql .= " AND c.chronic_disease_notes IS NOT NULL AND TRIM(c.chronic_disease_notes) != ''";
        }

        if (!empty($search)) {
            $sql .= " AND (c.name LIKE ? OR c.code LIKE ? OR c.phone LIKE ? OR c.email LIKE ? OR c.allergy_notes LIKE ?)";
            $term = "%{$search}%";
            $params = array_merge($params, [$term, $term, $term, $term, $term]);
        }

        $sql .= " GROUP BY c.id ORDER BY c.created_at DESC";

        return $this->db->query($sql, $params);
    }

    /**
     * Get paginated customer records
     */
    public function getCustomersPaginated(?string $search = null, ?string $allergyOnly = 'all', ?string $status = 'all', int $page = 1, int $perPage = 25): array
    {
        $page = max(1, $page);
        $perPage = max(1, min(100, $perPage));
        $offset = ($page - 1) * $perPage;

        $where = "WHERE 1=1";
        $params = [];

        if ($status === 'active') {
            $where .= " AND c.is_active = 1";
        } elseif ($status === 'inactive') {
            $where .= " AND c.is_active = 0";
        }

        if ($allergyOnly === 'allergy') {
            $where .= " AND c.allergy_notes IS NOT NULL AND TRIM(c.allergy_notes) != ''";
        } elseif ($allergyOnly === 'chronic') {
            $where .= " AND c.chronic_disease_notes IS NOT NULL AND TRIM(c.chronic_disease_notes) != ''";
        }

        if (!empty($search)) {
            $where .= " AND (c.name LIKE ? OR c.code LIKE ? OR c.phone LIKE ? OR c.email LIKE ? OR c.allergy_notes LIKE ?)";
            $term = "%{$search}%";
            $params = array_merge($params, [$term, $term, $term, $term, $term]);
        }

        $countSql = "SELECT COUNT(*) as total FROM {$this->table} c {$where}";
        $countRow = $this->db->fetch($countSql, $params);
        $total = $countRow ? (int)$countRow['total'] : 0;

        $dataSql = "SELECT c.*,
                           COUNT(DISTINCT s.id) as real_sales_count,
                           COUNT(DISTINCT rx.id) as real_prescription_count
                    FROM {$this->table} c
                    LEFT JOIN sales s ON s.customer_phone = c.phone OR s.customer_name = c.name
                    LEFT JOIN prescriptions rx ON rx.patient_name = c.name
                    {$where}
                    GROUP BY c.id 
                    ORDER BY c.created_at DESC 
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
     * Get aggregate statistics for CRM dashboard
     */
    public function getCrmStats(): array
    {
        $total = $this->db->fetch("SELECT COUNT(*) as cnt FROM {$this->table}")['cnt'] ?? 0;
        $active = $this->db->fetch("SELECT COUNT(*) as cnt FROM {$this->table} WHERE is_active = 1")['cnt'] ?? 0;
        $allergy = $this->db->fetch("SELECT COUNT(*) as cnt FROM {$this->table} WHERE allergy_notes IS NOT NULL AND TRIM(allergy_notes) != ''")['cnt'] ?? 0;
        $chronic = $this->db->fetch("SELECT COUNT(*) as cnt FROM {$this->table} WHERE chronic_disease_notes IS NOT NULL AND TRIM(chronic_disease_notes) != ''")['cnt'] ?? 0;
        $totalSpend = $this->db->fetch("SELECT COALESCE(SUM(total_spend), 0) as total FROM {$this->table}")['total'] ?? 0;

        return [
            'total_customers' => (int)$total,
            'active_customers' => (int)$active,
            'allergy_flagged' => (int)$allergy,
            'chronic_patients' => (int)$chronic,
            'lifetime_spend' => (float)$totalSpend
        ];
    }

    /**
     * Create new customer / patient
     */
    public function createCustomer(array $data): int
    {
        if (empty($data['code'])) {
            $data['code'] = $this->generateCode();
        }

        return $this->create([
            'code' => strtoupper(trim($data['code'])),
            'name' => trim($data['name']),
            'phone' => !empty($data['phone']) ? trim($data['phone']) : null,
            'email' => !empty($data['email']) ? trim($data['email']) : null,
            'gender' => in_array($data['gender'] ?? '', ['male', 'female', 'other']) ? $data['gender'] : 'other',
            'birth_date' => !empty($data['birth_date']) ? $data['birth_date'] : null,
            'address' => !empty($data['address']) ? trim($data['address']) : null,
            'allergy_notes' => !empty($data['allergy_notes']) ? trim($data['allergy_notes']) : null,
            'chronic_disease_notes' => !empty($data['chronic_disease_notes']) ? trim($data['chronic_disease_notes']) : null,
            'total_spend' => 0.00,
            'total_visits' => 0,
            'is_active' => isset($data['is_active']) ? (int)(bool)$data['is_active'] : 1
        ]);
    }

    /**
     * Update customer / patient details
     */
    public function updateCustomer(int $id, array $data): bool
    {
        $updateData = [];
        if (isset($data['name'])) $updateData['name'] = trim($data['name']);
        if (isset($data['phone'])) $updateData['phone'] = trim($data['phone']);
        if (isset($data['email'])) $updateData['email'] = trim($data['email']);
        if (isset($data['gender'])) $updateData['gender'] = in_array($data['gender'], ['male', 'female', 'other']) ? $data['gender'] : 'other';
        if (array_key_exists('birth_date', $data)) $updateData['birth_date'] = !empty($data['birth_date']) ? $data['birth_date'] : null;
        if (isset($data['address'])) $updateData['address'] = trim($data['address']);
        if (isset($data['allergy_notes'])) $updateData['allergy_notes'] = trim($data['allergy_notes']);
        if (isset($data['chronic_disease_notes'])) $updateData['chronic_disease_notes'] = trim($data['chronic_disease_notes']);
        if (isset($data['is_active'])) $updateData['is_active'] = (int)(bool)$data['is_active'];

        return $this->update($id, $updateData);
    }

    /**
     * Delete or Deactivate customer
     */
    public function deleteCustomer(int $id): bool
    {
        // Check if customer has linked sales transactions
        $cust = $this->find($id);
        if (!$cust) return false;

        $hasSales = $this->db->fetch("SELECT id FROM sales WHERE customer_phone = ? OR customer_name = ? LIMIT 1", [$cust['phone'], $cust['name']]);

        if ($hasSales) {
            // Soft deactivate
            return $this->update($id, ['is_active' => 0]);
        }

        return $this->delete($id);
    }

    /**
     * Get customer details with full medication history
     */
    public function getDetails(int $id): ?array
    {
        $customer = $this->find($id);
        if (!$customer) return null;

        // Fetch recent sales transactions
        $sales = $this->db->query(
            "SELECT s.*, u.full_name as cashier_name 
             FROM sales s 
             JOIN users u ON s.user_id = u.id 
             WHERE s.customer_phone = ? OR s.customer_name = ?
             ORDER BY s.created_at DESC LIMIT 20",
            [$customer['phone'], $customer['name']]
        );

        // Fetch prescription records
        $prescriptions = $this->db->query(
            "SELECT rx.*, u.full_name as reviewer_name 
             FROM prescriptions rx 
             LEFT JOIN users u ON rx.pharmacist_id = u.id 
             WHERE rx.patient_name = ?
             ORDER BY rx.created_at DESC LIMIT 20",
            [$customer['name']]
        );

        $customer['sales_history'] = $sales;
        $customer['prescription_history'] = $prescriptions;

        return $customer;
    }

    /**
     * Search customers for POS / Prescription autocomplete
     */
    public function search(string $query, int $limit = 10): array
    {
        $term = "%{$query}%";
        $sql = "SELECT id, code, name, phone, email, allergy_notes, chronic_disease_notes, address 
                FROM {$this->table} 
                WHERE is_active = 1 AND (name LIKE ? OR phone LIKE ? OR code LIKE ?)
                ORDER BY name ASC LIMIT ?";
        return $this->db->query($sql, [$term, $term, $term, $limit]);
    }
}
