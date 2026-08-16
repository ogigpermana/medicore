<?php

namespace App\Middleware;

use Core\Request;
use Core\Response;
use Core\MiddlewareInterface;
use Core\Session;

/**
 * CSRF Protection Middleware
 * Protects against Cross-Site Request Forgery attacks
 */

class CsrfMiddleware implements MiddlewareInterface
{
    private Session $session;
    private Request $request;

    public function __construct(?Session $session = null)
    {
        $this->session = $session ?? new Session();
    }

    /**
     * Handle incoming request
     */
    public function handle(Request $request, callable $next): ?Response
    {
        $this->request = $request;

        // Skip CSRF for GET, HEAD, OPTIONS requests
        if (in_array($request->getMethod(), ['GET', 'HEAD', 'OPTIONS'])) {
            return $next($request);
        }

        // Verify CSRF token for POST, PUT, PATCH, DELETE requests
        if (!$this->verifyCsrfToken($request)) {
            return $this->csrfErrorResponse();
        }

        // Generate new CSRF token for next request
        $this->regenerateCsrfToken();

        return $next($request);
    }

    /**
     * Verify CSRF token from request
     */
    private function verifyCsrfToken(Request $request): bool
    {
        $token = $this->getCsrfTokenFromRequest($request);
        $sessionToken = $this->session->get('_csrf_token');

        if (!$token || !$sessionToken) {
            return false;
        }

        return hash_equals($sessionToken, $token);
    }

    /**
     * Get CSRF token from request
     */
    private function getCsrfTokenFromRequest(Request $request): ?string
    {
        // Check in headers first (preferred for AJAX)
        $headerToken = $request->header('X-CSRF-Token');
        if ($headerToken) {
            return $headerToken;
        }

        // Check in POST data
        $postToken = $request->get('_token');
        if ($postToken) {
            return $postToken;
        }

        return null;
    }

    /**
     * Generate CSRF token
     */
    public function generateCsrfToken(): string
    {
        if (!$this->session->has('_csrf_token')) {
            $this->session->set('_csrf_token', $this->generateRandomToken());
        }
        return $this->session->get('_csrf_token');
    }

    /**
     * Regenerate CSRF token
     */
    private function regenerateCsrfToken(): void
    {
        $this->session->set('_csrf_token', $this->generateRandomToken());
    }

    /**
     * Generate random token
     */
    private function generateRandomToken(): string
    {
        return bin2hex(random_bytes(32));
    }

    /**
     * Get CSRF token (for use in views)
     */
    public function getCsrfToken(): string
    {
        return $this->generateCsrfToken();
    }

    /**
     * Get CSRF token field HTML
     */
    public function csrfField(): string
    {
        $token = $this->getCsrfToken();
        return '<input type="hidden" name="_token" value="' . htmlspecialchars($token) . '">';
    }

    /**
     * Return CSRF error response
     */
    private function csrfErrorResponse(): Response
    {
        if ($this->requestExpectsJson()) {
            return new Response(json_encode([
                'success' => false,
                'message' => 'CSRF token validation failed'
            ]), 419, ['Content-Type' => 'application/json']);
        }

        return new Response('CSRF token validation failed', 419);
    }

    /**
     * Check if request expects JSON response
     */
    private function requestExpectsJson(): bool
    {
        return $this->request->expectsJson();
    }
}