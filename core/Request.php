<?php

namespace Core;

/**
 * Request Class
 * Handles HTTP request data and parameters
 */

class Request
{
    private string $method = 'GET';
    private string $uri = '/';
    private array $params = [];
    private array $data = [];
    private array $headers = [];
    private ?Container $container = null;

    public function __construct(array $data = [], array $headers = [], string $method = 'GET', string $uri = '/')
    {
        $this->data = $data;
        $this->headers = $headers;
        $this->method = $method;
        $this->uri = $uri;
    }

    /**
     * Capture current HTTP request
     */
    public static function capture(): self
    {
        $request = new self();
        $request->method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $request->uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        $request->data = [...($_GET ?? []), ...($_POST ?? [])];
        $request->headers = function_exists('getallheaders') ? getallheaders() : [];
        
        // Parse JSON body for POST/PUT/PATCH/DELETE
        if (in_array($request->method, ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            $rawInput = file_get_contents('php://input');
            if (!empty($rawInput)) {
                $jsonData = json_decode($rawInput, true);
                if (is_array($jsonData)) {
                    $request->data = array_merge($request->data, $jsonData);
                }
            }
        }

        return $request;
    }

    /**
     * Set request data (for testing)
     */
    public function setData(array $data): void
    {
        $this->data = $data;
    }

    /**
     * Set request headers (for testing)
     */
    public function setHeaders(array $headers): void
    {
        $this->headers = $headers;
    }

    /**
     * Set request method (for testing)
     */
    public function setMethod(string $method): void
    {
        $this->method = $method;
    }

    /**
     * Set request URI (for testing)
     */
    public function setUri(string $uri): void
    {
        $this->uri = $uri;
    }

    /**
     * Set container instance
     */
    public function setContainer(Container $container): void
    {
        $this->container = $container;
    }

    /**
     * Get container instance
     */
    public function getContainer(): Container
    {
        if ($this->container === null) {
            throw new \Exception('Container not set in request');
        }
        return $this->container;
    }

    /**
     * Get request method
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Get request URI
     */
    public function getUri(): string
    {
        return $this->uri;
    }

    /**
     * Set route parameters
     */
    public function setParams(array $params): void
    {
        $this->params = $params;
    }

    /**
     * Get route parameter
     */
    public function param(string $key, mixed $default = null): mixed
    {
        return $this->params[$key] ?? $default;
    }

    /**
     * Check if route parameter exists
     */
    public function hasParam(string $key): bool
    {
        return isset($this->params[$key]);
    }

    /**
     * Get all route parameters
     */
    public function params(): array
    {
        return $this->params;
    }

    /**
     * Get request data (GET/POST/JSON)
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->data[$key] ?? $default;
    }

    /**
     * Get query / input parameter alias
     */
    public function query(string $key, mixed $default = null): mixed
    {
        return $this->get($key, $default);
    }

    /**
     * Get input parameter alias
     */
    public function input(string $key, mixed $default = null): mixed
    {
        return $this->get($key, $default);
    }

    /**
     * Get all request data
     */
    public function all(): array
    {
        return $this->data;
    }

    /**
     * Check if request has data
     */
    public function has(string $key): bool
    {
        return isset($this->data[$key]);
    }

    /**
     * Get request header
     */
    public function header(string $key, mixed $default = null): mixed
    {
        return $this->headers[$key] ?? $default;
    }

    /**
     * Check if request expects JSON response
     */
    public function expectsJson(): bool
    {
        return isset($this->headers['Accept']) && 
               str_contains($this->headers['Accept'], 'application/json');
    }

    /**
     * Authenticated user from session or JWT
     */
    private ?array $user = null;

    public function setUser(array $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function user(): ?array
    {
        return $this->user;
    }

    /**
     * Check if request is AJAX
     */
    public function isAjax(): bool
    {
        return isset($this->headers['X-Requested-With']) && 
               $this->headers['X-Requested-With'] === 'XMLHttpRequest';
    }
}