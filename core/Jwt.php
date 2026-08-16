<?php

namespace Core;

use Firebase\JWT\JWT as FirebaseJWT;
use Firebase\JWT\Key;

/**
 * JWT Authentication Utility
 * Issues and validates stateless JSON Web Tokens for API authentication
 */
class Jwt
{
    private string $secret;
    private string $algorithm;
    private int $defaultExpiry;

    public function __construct(?string $secret = null, string $algorithm = 'HS256', int $defaultExpiry = 3600)
    {
        $this->secret = $secret ?? (getenv('JWT_SECRET') ?: 'your-secret-key-change-in-production');
        $this->algorithm = $algorithm;
        $this->defaultExpiry = (int)(getenv('JWT_EXPIRY') ?: $defaultExpiry);
    }

    /**
     * Generate access token for authenticated user
     */
    public function generateToken(array $user, ?int $expiry = null): string
    {
        $now = time();
        $exp = $now + ($expiry ?? $this->defaultExpiry);

        $payload = [
            'iss' => 'medicore-pharmacy-erp',
            'aud' => 'medicore-api',
            'iat' => $now,
            'nbf' => $now,
            'exp' => $exp,
            'sub' => (string)($user['id'] ?? $user['user_id'] ?? ''),
            'user' => [
                'id' => $user['id'] ?? $user['user_id'] ?? null,
                'email' => $user['email'] ?? '',
                'full_name' => $user['full_name'] ?? $user['name'] ?? '',
                'role' => $user['role_name'] ?? $user['role'] ?? 'cashier'
            ]
        ];

        return FirebaseJWT::encode($payload, $this->secret, $this->algorithm);
    }

    /**
     * Generate long-lived refresh token (7 days)
     */
    public function generateRefreshToken(array $user, int $expiry = 604800): string
    {
        $now = time();
        $payload = [
            'iss' => 'medicore-pharmacy-erp',
            'aud' => 'medicore-refresh',
            'iat' => $now,
            'exp' => $now + $expiry,
            'sub' => (string)($user['id'] ?? $user['user_id'] ?? ''),
            'type' => 'refresh'
        ];

        return FirebaseJWT::encode($payload, $this->secret, $this->algorithm);
    }

    /**
     * Validate and decode token
     * Returns decoded payload array or null if expired/invalid
     */
    public function validateToken(string $token, bool $isRefresh = false): ?array
    {
        try {
            $decoded = FirebaseJWT::decode($token, new Key($this->secret, $this->algorithm));
            $payload = (array)$decoded;
            
            // Check audience
            $expectedAud = $isRefresh ? 'medicore-refresh' : 'medicore-api';
            if (($payload['aud'] ?? '') !== $expectedAud) {
                return null;
            }

            if (isset($payload['user']) && is_object($payload['user'])) {
                $payload['user'] = (array)$payload['user'];
            }

            return $payload;
        } catch (\Throwable $e) {
            return null;
        }
    }
}
