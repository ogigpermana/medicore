<?php

namespace App\Models;

use Core\Model;
use Core\Database;

/**
 * AuditLog Model
 * Handles enterprise compliance auditing, user actions, security events, and state mutations
 */
class AuditLog extends Model
{
    protected string $table = 'audit_logs';
    protected string $primaryKey = 'id';
    protected array $fillable = [
        'user_id',
        'action',
        'entity_type',
        'entity_id',
        'description',
        'ip_address',
        'user_agent',
        'metadata',
        'created_at'
    ];

    public function __construct(?Database $db = null)
    {
        parent::__construct($db);
    }

    /**
     * Get paginated audit logs with search and multi-criteria filters
     */
    public function getPaginatedLogs(array $filters = [], int $page = 1, int $perPage = 25): array
    {
        $page = max(1, $page);
        $perPage = max(1, min(100, $perPage));
        $offset = ($page - 1) * $perPage;

        $where = "WHERE 1=1";
        $params = [];

        if (!empty($filters['search'])) {
            $where .= " AND (a.action LIKE ? OR a.description LIKE ? OR a.entity_type LIKE ? OR a.ip_address LIKE ? OR u.full_name LIKE ? OR u.email LIKE ?)";
            $term = "%{$filters['search']}%";
            $params = array_merge($params, [$term, $term, $term, $term, $term, $term]);
        }

        if (!empty($filters['action_type']) && $filters['action_type'] !== 'all') {
            $where .= " AND a.action LIKE ?";
            $params[] = "%{$filters['action_type']}%";
        }

        if (!empty($filters['user_id'])) {
            $where .= " AND a.user_id = ?";
            $params[] = (int)$filters['user_id'];
        }

        if (!empty($filters['start_date'])) {
            $where .= " AND DATE(a.created_at) >= ?";
            $params[] = $filters['start_date'];
        }

        if (!empty($filters['end_date'])) {
            $where .= " AND DATE(a.created_at) <= ?";
            $params[] = $filters['end_date'];
        }

        // Count total matching logs
        $countSql = "SELECT COUNT(*) as total 
                     FROM {$this->table} a 
                     LEFT JOIN users u ON a.user_id = u.id 
                     {$where}";
        $countRow = $this->db->fetch($countSql, $params);
        $total = $countRow ? (int)$countRow['total'] : 0;

        // Fetch log records with user metadata
        $dataSql = "SELECT a.*, u.full_name as user_name, u.email as user_email, r.name as role_name
                    FROM {$this->table} a
                    LEFT JOIN users u ON a.user_id = u.id
                    LEFT JOIN user_roles ur ON u.id = ur.user_id
                    LEFT JOIN roles r ON ur.role_id = r.id
                    {$where}
                    ORDER BY a.created_at DESC
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
     * Get aggregate statistics for Audit Dashboard
     */
    public function getStats(): array
    {
        $total = $this->db->fetch("SELECT COUNT(*) as cnt FROM {$this->table}")['cnt'] ?? 0;
        $today = $this->db->fetch("SELECT COUNT(*) as cnt FROM {$this->table} WHERE DATE(created_at) = CURDATE()")['cnt'] ?? 0;
        $security = $this->db->fetch("SELECT COUNT(*) as cnt FROM {$this->table} WHERE action LIKE '%failed%' OR action LIKE '%locked%' OR action LIKE '%security%'")['cnt'] ?? 0;
        $auth = $this->db->fetch("SELECT COUNT(*) as cnt FROM {$this->table} WHERE action LIKE '%login%' OR action LIKE '%logout%' OR action LIKE '%auth%'")['cnt'] ?? 0;
        $uniqueUsers = $this->db->fetch("SELECT COUNT(DISTINCT user_id) as cnt FROM {$this->table} WHERE user_id IS NOT NULL")['cnt'] ?? 0;

        return [
            'total_events' => (int)$total,
            'today_events' => (int)$today,
            'security_alerts' => (int)$security,
            'auth_events' => (int)$auth,
            'active_users_audited' => (int)$uniqueUsers
        ];
    }

    /**
     * Record a system audit event
     */
    public function record(
        ?int $userId,
        string $action,
        string $entityType,
        ?int $entityId = null,
        ?string $description = null,
        array $metadata = []
    ): int {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'CLI/System';

        return $this->create([
            'user_id' => $userId,
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'description' => $description ?? "{$action} on {$entityType}",
            'ip_address' => $ip,
            'user_agent' => $ua,
            'metadata' => json_encode($metadata)
        ]);
    }
}
