<?php

namespace App\Models;

use Core\Model;
use Core\Database;

/**
 * User Model
 * Handles user data and authentication
 */

class User extends Model
{
    protected string $table = 'users';
    protected string $primaryKey = 'id';
    protected array $fillable = [
        'email',
        'password',
        'full_name',
        'phone',
        'avatar',
        'is_active',
        'email_verified',
        'email_verified_at',
        'email_verification_token',
        'email_verification_expires',
        'last_login_at',
        'failed_login_attempts',
        'locked_until'
    ];

    public function __construct(?Database $db = null)
    {
        parent::__construct($db);
    }

    /**
     * Find user by email
     */
    public function findByEmail(string $email): ?array
    {
        return $this->where('email', '=', $email)->first();
    }

    /**
     * Find user by ID with role
     */
    public function findWithRole(int $id): ?array
    {
        $sql = "SELECT u.*, r.name as role_name, r.permissions 
                FROM {$this->table} u
                LEFT JOIN user_roles ur ON u.id = ur.user_id
                LEFT JOIN roles r ON ur.role_id = r.id
                WHERE u.id = ? LIMIT 1";
        
        return $this->db->fetch($sql, [$id]);
    }

    /**
     * Find user by email with role
     */
    public function findByEmailWithRole(string $email): ?array
    {
        $sql = "SELECT u.*, r.name as role_name, r.permissions 
                FROM {$this->table} u
                LEFT JOIN user_roles ur ON u.id = ur.user_id
                LEFT JOIN roles r ON ur.role_id = r.id
                WHERE u.email = ? LIMIT 1";
        
        return $this->db->fetch($sql, [$email]);
    }

    /**
     * Create new user
     */
    public function register(array $data): int
    {
        // Hash password
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        $data['is_active'] = true;
        
        return $this->create($data);
    }

    /**
     * Verify user password
     */
    public function verifyPassword(string $email, string $password): bool
    {
        $user = $this->findByEmail($email);
        
        if (!$user) {
            return false;
        }

        return password_verify($password, $user['password']);
    }

    /**
     * Update user last login
     */
    public function updateLastLogin(int $userId): bool
    {
        return $this->update($userId, [
            'last_login_at' => date('Y-m-d H:i:s'),
            'failed_login_attempts' => 0,
            'locked_until' => null
        ]);
    }

    /**
     * Increment failed login attempts
     */
    public function incrementFailedAttempts(int $userId): bool
    {
        $user = $this->find($userId);
        $attempts = ($user['failed_login_attempts'] ?? 0) + 1;

        // Lock account after 5 failed attempts
        if ($attempts >= 5) {
            return $this->update($userId, [
                'failed_login_attempts' => $attempts,
                'locked_until' => date('Y-m-d H:i:s', strtotime('+30 minutes'))
            ]);
        }

        return $this->update($userId, [
            'failed_login_attempts' => $attempts
        ]);
    }

    /**
     * Check if account is locked
     */
    public function isLocked(int $userId): bool
    {
        $user = $this->find($userId);
        
        if (!$user || !$user['locked_until']) {
            return false;
        }

        // Check if lock has expired
        if (strtotime($user['locked_until']) < time()) {
            $this->update($userId, [
                'failed_login_attempts' => 0,
                'locked_until' => null
            ]);
            return false;
        }

        return true;
    }

    /**
     * Get remaining lock time in seconds
     */
    public function getLockTimeRemaining(int $userId): int
    {
        $user = $this->find($userId);
        
        if (!$user || !$user['locked_until']) {
            return 0;
        }

        $remaining = strtotime($user['locked_until']) - time();
        return max(0, $remaining);
    }

    /**
     * Update user profile
     */
    public function updateProfile(int $userId, array $data): bool
    {
        $allowedFields = ['full_name', 'phone', 'avatar'];
        $filteredData = array_intersect_key($data, array_flip($allowedFields));
        
        return $this->update($userId, $filteredData);
    }

    /**
     * Deactivate user
     */
    public function deactivate(int $userId): bool
    {
        return $this->update($userId, ['is_active' => false]);
    }

    /**
     * Activate user
     */
    public function activate(int $userId): bool
    {
        return $this->update($userId, ['is_active' => true]);
    }
}