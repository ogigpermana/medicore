<?php

namespace Core;

/**
 * Auth Class
 * Session-based authentication system
 */

class Auth
{
    private Session $session;
    private ?RememberMe $rememberMe = null;

    public function __construct(?Session $session = null, ?RememberMe $rememberMe = null)
    {
        $this->session = $session ?? app()->getContainer()->get(Session::class);
        $this->rememberMe = $rememberMe;
    }

    /**
     * Set RememberMe instance
     */
    public function setRememberMe(RememberMe $rememberMe): void
    {
        $this->rememberMe = $rememberMe;
    }

    /**
     * Auto-login from remember me token
     */
    public function attemptRememberMe(): bool
    {
        if (!$this->rememberMe) {
            return false;
        }

        $userData = $this->rememberMe->validateToken();
        
        if ($userData) {
            $this->login($userData);
            return true;
        }

        return false;
    }

    /**
     * Check if user is authenticated
     */
    public function check(): bool
    {
        if (!$this->session->has('user_id')) {
            return false;
        }

        // Check session timeout
        if ($this->isSessionExpired()) {
            $this->logout();
            return false;
        }

        return true;
    }

    /**
     * Check if session has expired
     */
    private function isSessionExpired(): bool
    {
        $sessionLifetime = 7200; // 2 hours in seconds (from config)
        $lastActivity = $this->session->get('last_activity');
        
        if (!$lastActivity) {
            $this->session->set('last_activity', time());
            return false;
        }

        $timeSinceActivity = time() - $lastActivity;
        
        if ($timeSinceActivity > $sessionLifetime) {
            return true;
        }

        // Update last activity
        $this->session->set('last_activity', time());
        return false;
    }

    /**
     * Get current authenticated user
     */
    public function user(): ?array
    {
        if (!$this->check()) {
            return null;
        }

        $role = $this->session->get('user_role_name') ?? $this->session->get('user_role') ?? 'staff';

        return [
            'id' => $this->session->get('user_id'),
            'email' => $this->session->get('user_email'),
            'name' => $this->session->get('user_full_name') ?? $this->session->get('user_name'),
            'full_name' => $this->session->get('user_full_name') ?? $this->session->get('user_name'),
            'role' => $role,
            'role_name' => $role,
            'role_id' => $this->session->get('user_role_id'),
            'permissions' => $this->session->get('user_permissions')
        ];
    }

    /**
     * Get user ID
     */
    public function id(): ?int
    {
        return $this->session->get('user_id');
    }

    /**
     * Attempt to login user
     */
    public function attempt(array $credentials): bool
    {
        // This will be implemented with User model
        // For now, return false as placeholder
        return false;
    }

    /**
     * Login user with user data
     */
    public function login(array $user): void
    {
        $fullName = $user['full_name'] ?? $user['name'] ?? $user['email'];
        $roleName = strtolower($user['role_name'] ?? $user['role'] ?? 'pharmacist');

        $this->session->set('user_id', $user['id']);
        $this->session->set('user_email', $user['email']);
        $this->session->set('user_name', $fullName);
        $this->session->set('user_full_name', $fullName);
        $this->session->set('user_role', $roleName);
        $this->session->set('user_role_name', $roleName);
        $this->session->set('user_role_id', $user['role_id'] ?? null);
        $this->session->set('user_permissions', $user['permissions'] ?? null);
        $this->session->set('logged_in_at', date('Y-m-d H:i:s'));
        $this->session->set('last_activity', time());
    }

    /**
     * Logout user
     */
    public function logout(): void
    {
        // Clear remember me cookie if RememberMe is available
        if ($this->rememberMe) {
            $this->rememberMe->clearCookie();
        }
        
        $this->session->clear();
    }

    /**
     * Check if user has specific role
     */
    public function hasRole(string $role): bool
    {
        return $this->session->get('user_role') === $role;
    }

    /**
     * Check if user has permission
     */
    public function can(string $permission): bool
    {
        $role = $this->session->get('user_role');
        $permissions = $this->session->get('user_permissions');
        
        // Superadmin can do everything
        if ($role === 'superadmin') {
            return true;
        }

        // If no permissions found, check against default permissions
        if (!$permissions) {
            return $this->checkDefaultPermissions($role, $permission);
        }

        // Parse JSON permissions
        $rolePermissions = json_decode($permissions, true);
        
        if (in_array('*', $rolePermissions)) {
            return true;
        }

        // Check for wildcard permissions
        foreach ($rolePermissions as $rolePermission) {
            if ($this->matchPermission($rolePermission, $permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check permission against default role permissions
     */
    private function checkDefaultPermissions(string $role, string $permission): bool
    {
        $defaultPermissions = [
            'superadmin' => ['*'],
            'owner' => ['products.*', 'sales.*', 'reports.*', 'customers.*', 'prescriptions.*', 'users.read'],
            'pharmacist' => ['products.read', 'products.write', 'prescriptions.*', 'customers.read', 'sales.read'],
            'cashier' => ['sales.*', 'products.read', 'customers.read'],
            'warehouse' => ['products.*', 'stock.*', 'suppliers.*']
        ];

        $rolePermissions = $defaultPermissions[$role] ?? [];
        
        if (in_array('*', $rolePermissions)) {
            return true;
        }

        foreach ($rolePermissions as $rolePermission) {
            if ($this->matchPermission($rolePermission, $permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Match permission with wildcard support
     */
    private function matchPermission(string $rolePermission, string $requestedPermission): bool
    {
        // Exact match
        if ($rolePermission === $requestedPermission) {
            return true;
        }

        // Wildcard match (e.g., "products.*" matches "products.delete")
        if (str_ends_with($rolePermission, '.*')) {
            $prefix = str_replace('.*', '', $rolePermission);
            return str_starts_with($requestedPermission, $prefix . '.');
        }

        return false;
    }
}