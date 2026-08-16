<?php

namespace App\Models;

use Core\Model;
use Core\Database;

/**
 * Supplier Model
 * Handles pharmaceutical distributors (PBF) and vendor lifecycle
 */
class Supplier extends Model
{
    protected string $table = 'suppliers';
    protected string $primaryKey = 'id';
    protected array $fillable = [
        'code',
        'name',
        'contact_person',
        'phone',
        'email',
        'address',
        'is_active'
    ];

    public function __construct(?Database $db = null)
    {
        parent::__construct($db);
    }

    /**
     * Get all active suppliers
     */
    public function getActive(): array
    {
        $sql = "SELECT s.*, 
                       COUNT(DISTINCT po.id) as total_po_count,
                       COALESCE(SUM(gr.total_amount), 0) as total_purchase_amount,
                       COALESCE(SUM(CASE WHEN gr.payment_status != 'paid' THEN (gr.total_amount - gr.amount_paid) ELSE 0 END), 0) as total_ap_outstanding
                FROM {$this->table} s
                LEFT JOIN purchase_orders po ON s.id = po.supplier_id
                LEFT JOIN goods_receipts gr ON s.id = gr.supplier_id
                WHERE s.is_active = 1 
                GROUP BY s.id
                ORDER BY s.name ASC";
        return $this->db->query($sql);
    }

    /**
     * Get all suppliers with optional search and status filter
     */
    public function getAllSuppliers(?string $search = null, ?string $status = 'all'): array
    {
        $sql = "SELECT s.*, 
                       COUNT(DISTINCT po.id) as total_po_count,
                       COALESCE(SUM(gr.total_amount), 0) as total_purchase_amount,
                       COALESCE(SUM(CASE WHEN gr.payment_status != 'paid' THEN (gr.total_amount - gr.amount_paid) ELSE 0 END), 0) as total_ap_outstanding
                FROM {$this->table} s
                LEFT JOIN purchase_orders po ON s.id = po.supplier_id
                LEFT JOIN goods_receipts gr ON s.id = gr.supplier_id
                WHERE 1=1";
        $params = [];

        if ($status === 'active') {
            $sql .= " AND s.is_active = 1";
        } elseif ($status === 'inactive') {
            $sql .= " AND s.is_active = 0";
        }

        if (!empty($search)) {
            $sql .= " AND (s.name LIKE ? OR s.code LIKE ? OR s.contact_person LIKE ? OR s.phone LIKE ?)";
            $searchTerm = "%{$search}%";
            $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
        }

        $sql .= " GROUP BY s.id ORDER BY s.name ASC";

        return $this->db->query($sql, $params);
    }

    /**
     * Generate sequential supplier code (e.g. PBF-001)
     */
    public function generateCode(): string
    {
        $row = $this->db->fetch("SELECT id FROM {$this->table} ORDER BY id DESC LIMIT 1");
        $nextId = ($row ? (int)$row['id'] : 0) + 1;
        return sprintf("PBF-%03d", $nextId);
    }

    /**
     * Create supplier record
     */
    public function createSupplier(array $data): int
    {
        if (empty($data['code'])) {
            $data['code'] = $this->generateCode();
        }

        return $this->create([
            'code' => strtoupper(trim($data['code'])),
            'name' => trim($data['name']),
            'contact_person' => !empty($data['contact_person']) ? trim($data['contact_person']) : null,
            'phone' => !empty($data['phone']) ? trim($data['phone']) : null,
            'email' => !empty($data['email']) ? trim($data['email']) : null,
            'address' => !empty($data['address']) ? trim($data['address']) : null,
            'is_active' => isset($data['is_active']) ? (int)(bool)$data['is_active'] : 1
        ]);
    }

    /**
     * Update supplier record
     */
    public function updateSupplier(int $id, array $data): bool
    {
        $updateData = [];
        if (isset($data['name'])) $updateData['name'] = trim($data['name']);
        if (isset($data['contact_person'])) $updateData['contact_person'] = trim($data['contact_person']);
        if (isset($data['phone'])) $updateData['phone'] = trim($data['phone']);
        if (isset($data['email'])) $updateData['email'] = trim($data['email']);
        if (isset($data['address'])) $updateData['address'] = trim($data['address']);
        if (isset($data['is_active'])) $updateData['is_active'] = (int)(bool)$data['is_active'];

        return $this->update($id, $updateData);
    }

    /**
     * Delete / Deactivate supplier
     */
    public function deleteSupplier(int $id): bool
    {
        // Check if supplier has linked purchase orders or goods receipts
        $hasPo = $this->db->fetch("SELECT id FROM purchase_orders WHERE supplier_id = ? LIMIT 1", [$id]);
        $hasGr = $this->db->fetch("SELECT id FROM goods_receipts WHERE supplier_id = ? LIMIT 1", [$id]);

        if ($hasPo || $hasGr) {
            // Soft deactivate to preserve historical ledger integrity
            return $this->update($id, ['is_active' => 0]);
        }

        return $this->delete($id);
    }
}
