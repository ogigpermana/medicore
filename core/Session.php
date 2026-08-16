<?php

namespace Core;

/**
 * Session Class
 * Handles session management
 */

class Session
{
    private bool $useRealSession;
    private array $testSessionData = [];

    public function __construct(bool $startSession = true)
    {
        $this->useRealSession = $startSession;
        if ($startSession && session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Set session value
     */
    public function set(string $key, mixed $value): void
    {
        if ($this->useRealSession) {
            $_SESSION[$key] = $value;
        } else {
            // Use array for testing
            $this->testSessionData[$key] = $value;
        }
    }

    /**
     * Get session value
     */
    public function get(string $key, mixed $default = null): mixed
    {
        if ($this->useRealSession) {
            return $_SESSION[$key] ?? $default;
        } else {
            return $this->testSessionData[$key] ?? $default;
        }
    }

    /**
     * Check if session key exists
     */
    public function has(string $key): bool
    {
        if ($this->useRealSession) {
            return isset($_SESSION[$key]);
        } else {
            return isset($this->testSessionData[$key]);
        }
    }

    /**
     * Remove session value
     */
    public function remove(string $key): void
    {
        if ($this->useRealSession) {
            unset($_SESSION[$key]);
        } else {
            unset($this->testSessionData[$key]);
        }
    }

    /**
     * Get CSRF token from session
     */
    public function getCsrfToken(): string
    {
        $token = $this->get('_token');
        if (!$token) {
            $token = bin2hex(random_bytes(32));
            $this->set('_token', $token);
        }
        return $token;
    }

    /**
     * Set CSRF token in session
     */
    public function setCsrfToken(string $token): void
    {
        $this->set('_token', $token);
    }

    /**
     * Get all session data
     */
    public function all(): array
    {
        if ($this->useRealSession) {
            return $_SESSION;
        } else {
            return $this->testSessionData;
        }
    }

    /**
     * Clear all session data
     */
    public function clear(): void
    {
        if ($this->useRealSession) {
            $_SESSION = [];
        } else {
            $this->testSessionData = [];
        }
    }

    /**
     * Destroy session
     */
    public function destroy(): void
    {
        if ($this->useRealSession) {
            session_destroy();
        } else {
            $this->testSessionData = [];
        }
    }

    /**
     * Flash message (one-time session data)
     */
    public function flash(string $key, mixed $value): void
    {
        if ($this->useRealSession) {
            $_SESSION['_flash'][$key] = $value;
        } else {
            $this->testSessionData['_flash'][$key] = $value;
        }
    }

    /**
     * Get flash message
     */
    public function getFlash(string $key, mixed $default = null): mixed
    {
        if ($this->useRealSession) {
            $value = $_SESSION['_flash'][$key] ?? $default;
            unset($_SESSION['_flash'][$key]);
            return $value;
        } else {
            $value = $this->testSessionData['_flash'][$key] ?? $default;
            unset($this->testSessionData['_flash'][$key]);
            return $value;
        }
    }
}