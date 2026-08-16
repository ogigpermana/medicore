<?php

namespace Core;

/**
 * Remember Me Token Manager
 * Handles persistent login tokens
 */

class RememberMe
{
    private Database $db;
    private Session $session;
    private int $tokenExpiryDays = 30;

    public function __construct(Database $db, Session $session)
    {
        $this->db = $db;
        $this->session = $session;
    }

    /**
     * Create remember me token for user
     */
    public function createToken(int $userId): string
    {
        $token = bin2hex(random_bytes(32));
        $selector = bin2hex(random_bytes(16));
        $hashedToken = hash('sha256', $token);
        $expires = date('Y-m-d H:i:s', strtotime('+' . $this->tokenExpiryDays . ' days'));

        $sql = "INSERT INTO remember_tokens (user_id, selector, token, expires_at) 
                VALUES (:user_id, :selector, :token, :expires_at)";
        
        $this->db->query($sql, [
            'user_id' => $userId,
            'selector' => $selector,
            'token' => $hashedToken,
            'expires_at' => $expires
        ]);

        // Set cookie
        $this->setCookie($selector, $token, $this->tokenExpiryDays * 86400);

        return $token;
    }

    /**
     * Validate remember me token and auto-login user
     */
    public function validateToken(): ?array
    {
        $cookieName = $this->getCookieName();
        
        if (!isset($_COOKIE[$cookieName])) {
            return null;
        }

        $cookieValue = $_COOKIE[$cookieName];
        $parts = explode(':', $cookieValue);
        
        if (count($parts) !== 2) {
            $this->clearCookie();
            return null;
        }

        [$selector, $token] = $parts;

        // Find token in database
        $sql = "SELECT rt.*, u.id, u.email, u.full_name, u.is_active, u.email_verified, r.name as role_name, r.slug as role_slug
                FROM remember_tokens rt
                JOIN users u ON rt.user_id = u.id
                LEFT JOIN user_roles ur ON u.id = ur.user_id
                LEFT JOIN roles r ON ur.role_id = r.id
                WHERE rt.selector = :selector 
                AND rt.expires_at > NOW()
                LIMIT 1";
        
        $result = $this->db->query($sql, ['selector' => $selector]);
        
        if (empty($result)) {
            $this->clearCookie();
            return null;
        }

        $rememberToken = $result[0];

        // Verify token hash
        if (!hash_equals($rememberToken['token'], hash('sha256', $token))) {
            $this->clearCookie();
            $this->deleteToken($rememberToken['id']);
            return null;
        }

        // Check if user is active
        if (!$rememberToken['is_active']) {
            $this->clearCookie();
            return null;
        }

        // Rotate token for security
        $this->rotateToken($rememberToken['id'], $rememberToken['user_id']);

        // Return user data
        return [
            'id' => $rememberToken['id'],
            'email' => $rememberToken['email'],
            'full_name' => $rememberToken['full_name'],
            'role' => $rememberToken['role_slug'] ?? 'user',
            'role_name' => $rememberToken['role_name'] ?? 'User'
        ];
    }

    /**
     * Rotate remember me token for security
     */
    private function rotateToken(int $tokenId, int $userId): void
    {
        $newToken = bin2hex(random_bytes(32));
        $newSelector = bin2hex(random_bytes(16));
        $hashedToken = hash('sha256', $newToken);
        $expires = date('Y-m-d H:i:s', strtotime('+' . $this->tokenExpiryDays . ' days'));

        $sql = "UPDATE remember_tokens 
                SET selector = :selector, token = :token, expires_at = :expires_at 
                WHERE id = :id";
        
        $this->db->query($sql, [
            'selector' => $newSelector,
            'token' => $hashedToken,
            'expires_at' => $expires,
            'id' => $tokenId
        ]);

        // Update cookie
        $this->setCookie($newSelector, $newToken, $this->tokenExpiryDays * 86400);
    }

    /**
     * Delete remember me token
     */
    public function deleteToken(int $tokenId): void
    {
        $sql = "DELETE FROM remember_tokens WHERE id = :id";
        $this->db->query($sql, ['id' => $tokenId]);
    }

    /**
     * Delete all remember me tokens for user
     */
    public function deleteAllUserTokens(int $userId): void
    {
        $sql = "DELETE FROM remember_tokens WHERE user_id = :user_id";
        $this->db->query($sql, ['user_id' => $userId]);
        
        $this->clearCookie();
    }

    /**
     * Set remember me cookie
     */
    private function setCookie(string $selector, string $token, int $expiry): void
    {
        $cookieName = $this->getCookieName();
        $cookieValue = $selector . ':' . $token;
        
        setcookie(
            $cookieName,
            $cookieValue,
            [
                'expires' => time() + $expiry,
                'path' => '/',
                'domain' => '',
                'secure' => false, // Set to false for local development
                'httponly' => true,
                'samesite' => 'Strict'
            ]
        );
    }

    /**
     * Clear remember me cookie
     */
    public function clearCookie(): void
    {
        $cookieName = $this->getCookieName();
        
        setcookie(
            $cookieName,
            '',
            [
                'expires' => time() - 3600,
                'path' => '/',
                'domain' => '',
                'secure' => false, // Set to false for local development
                'httponly' => true,
                'samesite' => 'Strict'
            ]
        );
    }

    /**
     * Get cookie name
     */
    private function getCookieName(): string
    {
        return 'remember_me_' . md5($this->session->getId());
    }

    /**
     * Clean expired tokens
     */
    public function cleanExpiredTokens(): int
    {
        $sql = "DELETE FROM remember_tokens WHERE expires_at < NOW()";
        $this->db->query($sql);
        
        return $this->db->lastInsertId();
    }
}