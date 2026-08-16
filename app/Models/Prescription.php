<?php

namespace App\Models;

use Core\Model;
use Core\Database;
use PDO;

/**
 * Prescription Model
 * Handles clinical digital prescriptions, pharmacist verification sign-off, and dispensing queue
 */
class Prescription extends Model
{
    protected string $table = 'prescriptions';
    protected string $primaryKey = 'id';
    protected array $fillable = [
        'prescription_number',
        'patient_name',
        'patient_age',
        'patient_gender',
        'patient_weight',
        'doctor_name',
        'doctor_sip',
        'doctor_clinic',
        'diagnosis',
        'clinical_notes',
        'status',
        'pharmacist_id',
        'pharmacist_notes',
        'total_amount',
        'tuslah_fee',
        'embalase_fee',
        'sale_id',
        'reviewed_at',
        'dispensed_at'
    ];

    public function __construct(?Database $db = null)
    {
        parent::__construct($db);
    }

    /**
     * Generate unique prescription number format RX-YYYYMMDD-XXXX
     */
    public function generateNumber(): string
    {
        $prefix = 'RX-' . date('Ymd') . '-';
        $sql = "SELECT prescription_number FROM {$this->table} 
                WHERE prescription_number LIKE ? 
                ORDER BY id DESC LIMIT 1";
        
        $latest = $this->db->fetch($sql, [$prefix . '%']);
        
        if ($latest && isset($latest['prescription_number'])) {
            $lastNum = (int)substr($latest['prescription_number'], -4);
            $nextNum = str_pad($lastNum + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNum = '0001';
        }

        return $prefix . $nextNum;
    }

    /**
     * Get Prescription Queue with filtering
     */
    public function getQueue(array $filters = []): array
    {
        $sql = "SELECT p.*, u.full_name as pharmacist_name,
                       (SELECT COUNT(id) FROM prescription_items WHERE prescription_id = p.id) as item_count,
                       (SELECT COUNT(id) FROM prescription_compounds WHERE prescription_id = p.id) as compound_count
                FROM {$this->table} p
                LEFT JOIN users u ON p.pharmacist_id = u.id
                WHERE 1=1";

        $params = [];

        if (!empty($filters['status'])) {
            $sql .= " AND p.status = ?";
            $params[] = $filters['status'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (p.prescription_number LIKE ? OR p.patient_name LIKE ? OR p.doctor_name LIKE ?)";
            $term = '%' . $filters['search'] . '%';
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
        }

        $sql .= " ORDER BY p.id DESC";

        if (!empty($filters['limit'])) {
            $sql .= " LIMIT " . (int)$filters['limit'];
        }

        return $this->db->query($sql, $params);
    }

    /**
     * Get Full Details of a Prescription with Finished Items and Compound Mixtures
     */
    public function getDetails(int $id): ?array
    {
        $sql = "SELECT p.*, u.full_name as pharmacist_name
                FROM {$this->table} p
                LEFT JOIN users u ON p.pharmacist_id = u.id
                WHERE p.id = ? LIMIT 1";
        
        $prescription = $this->db->fetch($sql, [$id]);
        if (!$prescription) return null;

        // Fetch Finished Drug Items
        $itemsSql = "SELECT pi.*, pr.sku, pr.name as product_name, pr.dosage, un.symbol as unit_symbol
                     FROM prescription_items pi
                     JOIN products pr ON pi.product_id = pr.id
                     LEFT JOIN units un ON pr.unit_id = un.id
                     WHERE pi.prescription_id = ?";
        $prescription['items'] = $this->db->query($itemsSql, [$id]);

        // Fetch Compounds / Racikan
        $compoundsSql = "SELECT * FROM prescription_compounds WHERE prescription_id = ?";
        $compounds = $this->db->query($compoundsSql, [$id]);

        foreach ($compounds as &$compound) {
            $compItemsSql = "SELECT pci.*, pr.sku, pr.name as product_name, pr.dosage, un.symbol as unit_symbol
                             FROM prescription_compound_items pci
                             JOIN products pr ON pci.product_id = pr.id
                             LEFT JOIN units un ON pr.unit_id = un.id
                             WHERE pci.compound_id = ?";
            $compound['ingredients'] = $this->db->query($compItemsSql, [$compound['id']]);
        }
        $prescription['compounds'] = $compounds;

        return $prescription;
    }

    /**
     * Create Complete Digital Prescription (Transaction Safe)
     */
    public function createPrescription(array $data, array $items = [], array $compounds = []): int
    {
        $pdo = $this->db->getPdo();
        $pdo->beginTransaction();

        try {
            if (empty($data['prescription_number'])) {
                $data['prescription_number'] = $this->generateNumber();
            }

            $totalAmount = 0.00;
            $totalTuslah = 0.00;
            $totalEmbalase = 0.00;

            // Calculate finished items subtotal
            foreach ($items as &$item) {
                $product = $this->db->fetch("SELECT sell_price FROM products WHERE id = ?", [$item['product_id']]);
                $unitPrice = (float)($product['sell_price'] ?? 0);
                $qty = (int)($item['quantity'] ?? 1);
                $item['unit_price'] = $unitPrice;
                $item['total_price'] = $unitPrice * $qty;
                $totalAmount += $item['total_price'];
            }

            // Calculate compound mixtures subtotal
            foreach ($compounds as &$compound) {
                $compFee = (float)($compound['compounding_fee'] ?? 5000);
                $packFee = (float)($compound['packaging_fee'] ?? 2000);
                $totalTuslah += $compFee;
                $totalEmbalase += $packFee;
                
                $ingTotal = 0.00;
                if (!empty($compound['ingredients']) && is_array($compound['ingredients'])) {
                    foreach ($compound['ingredients'] as &$ing) {
                        $p = $this->db->fetch("SELECT sell_price FROM products WHERE id = ?", [$ing['product_id']]);
                        $ing['unit_price'] = (float)($p['sell_price'] ?? 0);
                        $ing['subtotal'] = $ing['unit_price'] * (int)($ing['quantity_used'] ?? 1);
                        $ingTotal += $ing['subtotal'];
                    }
                }
                $compound['total_price'] = $ingTotal + $compFee + $packFee;
                $totalAmount += $compound['total_price'];
            }

            $data['total_amount'] = $totalAmount;
            $data['tuslah_fee'] = $totalTuslah;
            $data['embalase_fee'] = $totalEmbalase;

            // Insert master prescription
            $sql = "INSERT INTO {$this->table} (
                prescription_number, patient_name, patient_age, patient_gender, patient_weight,
                doctor_name, doctor_sip, doctor_clinic, diagnosis, clinical_notes,
                status, pharmacist_id, pharmacist_notes, total_amount, tuslah_fee, embalase_fee,
                created_at, updated_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $data['prescription_number'],
                $data['patient_name'],
                $data['patient_age'] ?? null,
                $data['patient_gender'] ?? 'male',
                $data['patient_weight'] ?? null,
                $data['doctor_name'],
                $data['doctor_sip'] ?? null,
                $data['doctor_clinic'] ?? null,
                $data['diagnosis'] ?? null,
                $data['clinical_notes'] ?? null,
                $data['status'] ?? 'pending',
                $data['pharmacist_id'] ?? null,
                $data['pharmacist_notes'] ?? null,
                $data['total_amount'],
                $data['tuslah_fee'],
                $data['embalase_fee']
            ]);

            $prescriptionId = (int)$pdo->lastInsertId();

            // Insert Finished Items
            if (!empty($items)) {
                $itemStmt = $pdo->prepare("INSERT INTO prescription_items (
                    prescription_id, product_id, dosage_instructions, usage_time, quantity, unit_price, total_price, notes
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

                foreach ($items as $it) {
                    $itemStmt->execute([
                        $prescriptionId,
                        $it['product_id'],
                        $it['dosage_instructions'] ?? '3x1 sehari sesudah makan',
                        $it['usage_time'] ?? 'Sesudah Makan',
                        $it['quantity'] ?? 1,
                        $it['unit_price'],
                        $it['total_price'],
                        $it['notes'] ?? null
                    ]);
                }
            }

            // Insert Compounds & Ingredients
            if (!empty($compounds)) {
                $compStmt = $pdo->prepare("INSERT INTO prescription_compounds (
                    prescription_id, compound_name, packaging_type, quantity_pack, dosage_instructions,
                    compounding_fee, packaging_fee, total_price, notes
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

                $ingStmt = $pdo->prepare("INSERT INTO prescription_compound_items (
                    compound_id, product_id, dose_per_pack, quantity_used, unit_price, subtotal
                ) VALUES (?, ?, ?, ?, ?, ?)");

                foreach ($compounds as $cmp) {
                    $compStmt->execute([
                        $prescriptionId,
                        $cmp['compound_name'],
                        $cmp['packaging_type'] ?? 'puyer',
                        $cmp['quantity_pack'] ?? 10,
                        $cmp['dosage_instructions'] ?? '3x1 bungkus sesudah makan',
                        $cmp['compounding_fee'] ?? 5000,
                        $cmp['packaging_fee'] ?? 2000,
                        $cmp['total_price'],
                        $cmp['notes'] ?? null
                    ]);

                    $compoundId = (int)$pdo->lastInsertId();

                    if (!empty($cmp['ingredients'])) {
                        foreach ($cmp['ingredients'] as $ing) {
                            $ingStmt->execute([
                                $compoundId,
                                $ing['product_id'],
                                $ing['dose_per_pack'] ?? null,
                                $ing['quantity_used'] ?? 1,
                                $ing['unit_price'],
                                $ing['subtotal']
                            ]);
                        }
                    }
                }
            }

            $pdo->commit();
            return $prescriptionId;
        } catch (\Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Pharmacist Clinical Review & Sign-Off Verification
     */
    public function reviewAndSignOff(int $id, int $pharmacistId, string $notes, string $nextStatus = 'reviewed'): bool
    {
        $sql = "UPDATE {$this->table} 
                SET status = ?, pharmacist_id = ?, pharmacist_notes = ?, reviewed_at = NOW(), updated_at = NOW() 
                WHERE id = ?";

        return $this->db->execute($sql, [$nextStatus, $pharmacistId, $notes, $id]);
    }

    /**
     * Update Prescription Status
     */
    public function updateStatus(int $id, string $status): bool
    {
        $extra = "";
        $params = [$status];

        if ($status === 'dispensed') {
            $extra = ", dispensed_at = NOW()";
        }

        $sql = "UPDATE {$this->table} SET status = ? {$extra}, updated_at = NOW() WHERE id = ?";
        $params[] = $id;

        return $this->db->execute($sql, $params);
    }

    /**
     * Get Queue Metrics / Status Counts
     */
    public function getCountsByStatus(): array
    {
        $sql = "SELECT status, COUNT(id) as count FROM {$this->table} GROUP BY status";
        $rows = $this->db->query($sql);
        
        $counts = [
            'all' => 0,
            'pending' => 0,
            'reviewed' => 0,
            'compounding' => 0,
            'ready' => 0,
            'dispensed' => 0,
            'cancelled' => 0
        ];

        foreach ($rows as $r) {
            $counts[$r['status']] = (int)$r['count'];
            $counts['all'] += (int)$r['count'];
        }

        return $counts;
    }

    /**
     * Find by prescription number for POS Checkout
     */
    public function findByNumber(string $rxNumber): ?array
    {
        $sql = "SELECT id FROM {$this->table} WHERE prescription_number = ? LIMIT 1";
        $res = $this->db->fetch($sql, [$rxNumber]);
        if (!$res) return null;

        return $this->getDetails($res['id']);
    }
}
