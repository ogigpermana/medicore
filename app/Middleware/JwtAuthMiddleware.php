<?php

namespace App\Middleware;

use Core\MiddlewareInterface;
use Core\Request;
use Core\Response;
use Core\Jwt;

/**
 * JwtAuthMiddleware
 * Protects REST API endpoints with Bearer JWT token validation
 */
class JwtAuthMiddleware implements MiddlewareInterface
{
    private Jwt $jwt;

    public function __construct(?Jwt $jwt = null)
    {
        $this->jwt = $jwt ?? new Jwt();
    }

    public function handle(Request $request, callable $next): ?Response
    {
        $authHeader = $request->header('Authorization') ?? $_SERVER['HTTP_AUTHORIZATION'] ?? '';

        if (empty($authHeader) || !preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            return Response::json([
                'success' => false,
                'message' => 'Unauthorized: Missing or invalid Authorization Bearer header'
            ], 401);
        }

        $token = trim($matches[1]);
        $payload = $this->jwt->validateToken($token);

        if (!$payload) {
            return Response::json([
                'success' => false,
                'message' => 'Unauthorized: Expired or invalid JWT token'
            ], 401);
        }

        // Store user payload on request attributes
        $request->setUser($payload['user'] ?? []);

        return $next($request);
    }
}
