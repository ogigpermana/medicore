<?php

namespace App\Middleware;

use Core\Request;
use Core\Response;
use Core\MiddlewareInterface;
use Core\Auth;

/**
 * Auth Middleware
 * Protects routes requiring authentication
 */

class AuthMiddleware implements MiddlewareInterface
{
    private Auth $auth;

    public function __construct(?Auth $auth = null)
    {
        $this->auth = $auth ?? new Auth();
    }

    /**
     * Handle incoming request
     */
    public function handle(Request $request, callable $next): ?Response
    {
        // Check if user is authenticated
        if (!$this->auth->check()) {
            return $this->unauthenticatedResponse();
        }

        // Check if user account is active (optional, based on requirements)
        $user = $this->auth->user();
        if ($user && isset($user['is_active']) && !$user['is_active']) {
            return $this->inactiveAccountResponse();
        }

        return $next($request);
    }

    /**
     * Return unauthenticated response
     */
    private function unauthenticatedResponse(): Response
    {
        if ($this->requestExpectsJson()) {
            return new Response(json_encode([
                'success' => false,
                'message' => 'Authentication required'
            ]), 401, ['Content-Type' => 'application/json']);
        }

        return new Response('Authentication required', 401);
    }

    /**
     * Return inactive account response
     */
    private function inactiveAccountResponse(): Response
    {
        if ($this->requestExpectsJson()) {
            return new Response(json_encode([
                'success' => false,
                'message' => 'Account is inactive'
            ]), 403, ['Content-Type' => 'application/json']);
        }

        return new Response('Account is inactive', 403);
    }

    /**
     * Check if request expects JSON response
     */
    private function requestExpectsJson(): bool
    {
        $request = Request::capture();
        return $request->expectsJson();
    }
}