<?php

namespace Core;

/**
 * Rate Limiter Class
 * Prevents brute force attacks by limiting request frequency
 */

class RateLimiter
{
    private array $attempts = [];
    private int $maxAttempts;
    private int $decayMinutes;

    public function __construct(int $maxAttempts = 5, int $decayMinutes = 1)
    {
        $this->maxAttempts = $maxAttempts;
        $this->decayMinutes = $decayMinutes;
    }

    /**
     * Check if request should be rate limited
     */
    public function attempt(string $key): bool
    {
        $this->cleanExpiredAttempts();

        if (!isset($this->attempts[$key])) {
            $this->attempts[$key] = [
                'attempts' => 0,
                'first_attempt' => time()
            ];
        }

        $this->attempts[$key]['attempts']++;

        return $this->attempts[$key]['attempts'] > $this->maxAttempts;
    }

    /**
     * Get number of remaining attempts
     */
    public function remaining(string $key): int
    {
        $this->cleanExpiredAttempts();

        if (!isset($this->attempts[$key])) {
            return $this->maxAttempts;
        }

        return max(0, $this->maxAttempts - $this->attempts[$key]['attempts']);
    }

    /**
     * Get time until rate limit resets (in seconds)
     */
    public function availableIn(string $key): int
    {
        $this->cleanExpiredAttempts();

        if (!isset($this->attempts[$key])) {
            return 0;
        }

        $resetTime = $this->attempts[$key]['first_attempt'] + ($this->decayMinutes * 60);
        $remaining = $resetTime - time();

        return max(0, $remaining);
    }

    /**
     * Clear rate limit for a key
     */
    public function clear(string $key): void
    {
        unset($this->attempts[$key]);
    }

    /**
     * Clean expired attempts
     */
    private function cleanExpiredAttempts(): void
    {
        $now = time();
        $decaySeconds = $this->decayMinutes * 60;

        foreach ($this->attempts as $key => $data) {
            if ($now - $data['first_attempt'] > $decaySeconds) {
                unset($this->attempts[$key]);
            }
        }
    }

    /**
     * Get current attempts count
     */
    public function attempts(string $key): int
    {
        $this->cleanExpiredAttempts();
        return $this->attempts[$key]['attempts'] ?? 0;
    }
}