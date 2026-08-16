<?php

namespace Core;

/**
 * Response Class
 * Handles HTTP response data and sending
 */

class Response
{
    private string $content;
    private int $status;
    private array $headers;

    public function __construct(string $content = '', int $status = 200, array $headers = [])
    {
        $this->content = $content;
        $this->status = $status;
        $this->headers = $headers;
    }

    /**
     * Create JSON response
     */
    public static function json(array $data, int $status = 200): self
    {
        return new self(json_encode($data), $status, [
            'Content-Type' => 'application/json'
        ]);
    }

    /**
     * Create redirect response
     */
    public static function redirect(string $url, int $status = 302): self
    {
        return new self('', $status, [
            'Location' => $url
        ]);
    }

    /**
     * Set response content
     */
    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    /**
     * Set response status
     */
    public function setStatus(int $status): self
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Set response header
     */
    public function setHeader(string $key, string $value): self
    {
        $this->headers[$key] = $value;
        return $this;
    }

    /**
     * Send the response to the client
     */
    public function send(): void
    {
        // Default enterprise security headers
        $securityHeaders = [
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'SAMEORIGIN',
            'X-XSS-Protection' => '1; mode=block',
            'Referrer-Policy' => 'strict-origin-when-cross-origin'
        ];

        foreach ($securityHeaders as $secKey => $secVal) {
            if (!isset($this->headers[$secKey])) {
                $this->headers[$secKey] = $secVal;
            }
        }

        // Set status code
        http_response_code($this->status);

        // Set headers
        foreach ($this->headers as $key => $value) {
            header("{$key}: {$value}");
        }

        // Send content
        echo $this->content;
    }

    /**
     * Get response content
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * Get response status
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * Get response status code (alias for getStatus)
     */
    public function getStatusCode(): int
    {
        return $this->status;
    }

    /**
     * Get response headers
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Get specific response header
     */
    public function getHeader(string $key, mixed $default = null): mixed
    {
        return $this->headers[$key] ?? $default;
    }
}