<?php

namespace Core;

/**
 * Audit Logger Class
 * Logs authentication and security events for compliance
 */

class AuditLogger
{
    private Database $db;
    private ?int $userId = null;

    public function __construct(?Database $db = null)
    {
        $this->db = $db ?? app()->getContainer()->get(Database::class);
    }

    /**
     * Set current user ID
     */
    public function setUserId(?int $userId): void
    {
        $this->userId = $userId;
    }

    /**
     * Log authentication event
     */
    public function logAuthEvent(string $action, array $data = []): void
    {
        $this->log('auth', $action, 'User', $this->userId, $data);
    }

    /**
     * Log user action
     */
    public function logUserAction(string $action, string $entityType, ?int $entityId = null, array $data = []): void
    {
        $this->log('user', $action, $entityType, $entityId, $data);
    }

    /**
     * Log security event
     */
    public function logSecurityEvent(string $action, array $data = []): void
    {
        $this->log('security', $action, 'System', null, $data);
    }

    /**
     * Generic log method
     */
    private function log(string $category, string $action, string $entityType, ?int $entityId, array $data): void
    {
        $sql = "INSERT INTO audit_logs (user_id, action, entity_type, entity_id, description, ip_address, user_agent, metadata, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";

        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        $description = $this->generateDescription($category, $action, $entityType, $data);
        $metadata = json_encode($data);

        try {
            $this->db->execute($sql, [
                $this->userId,
                $action,
                $entityType,
                $entityId,
                $description,
                $ip,
                $userAgent,
                $metadata
            ]);
        } catch (\Exception $e) {
            error_log('Audit log failed: ' . $e->getMessage());
        }
    }

    /**
     * Generate human-readable description
     */
    private function generateDescription(string $category, string $action, string $entityType, array $data): string
    {
        $descriptions = [
            'auth' => [
                'login' => 'User logged in',
                'logout' => 'User logged out',
                'failed_login' => 'Failed login attempt',
                'account_locked' => 'Account locked due to failed attempts',
                'password_changed' => 'User changed password',
                'password_reset' => 'User requested password reset'
            ],
            'user' => [
                'create' => 'Created new user',
                'update' => 'Updated user profile',
                'delete' => 'Deleted user',
                'deactivate' => 'Deactivated user account',
                'activate' => 'Activated user account'
            ],
            'security' => [
                'rate_limit_exceeded' => 'Rate limit exceeded',
                'suspicious_activity' => 'Suspicious activity detected',
                'permission_denied' => 'Permission denied for action'
            ]
        ];

        $categoryDescriptions = $descriptions[$category] ?? [];
        return $categoryDescriptions[$action] ?? "{$category}: {$action}";
    }

    /**
     * Get recent audit logs for a user
     */
    public function getUserLogs(int $userId, int $limit = 50): array
    {
        $sql = "SELECT * FROM audit_logs WHERE user_id = ? ORDER BY created_at DESC LIMIT ?";
        return $this->db->query($sql, [$userId, $limit]);
    }

    /**
     * Get recent security events
     */
    public function getSecurityEvents(int $limit = 100): array
    {
        $sql = "SELECT * FROM audit_logs WHERE action LIKE '%failed%' OR action LIKE '%locked%' ORDER BY created_at DESC LIMIT ?";
        return $this->db->query($sql, [$limit]);
    }
}