<?php

namespace Core;

/**
 * Configuration Manager
 * Loads and manages application configuration
 */

class Config
{
    private array $config = [];

    /**
     * Load configuration files from a directory
     */
    public function load(string $directory): void
    {
        $files = glob($directory . '/*.php');
        
        foreach ($files as $file) {
            $key = pathinfo($file, PATHINFO_FILENAME);
            $this->config[$key] = require $file;
        }
    }

    /**
     * Get a configuration value
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $keys = explode('.', $key);
        $value = $this->config;

        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return $default;
            }
            $value = $value[$k];
        }

        return $value;
    }

    /**
     * Set a configuration value
     */
    public function set(string $key, mixed $value): void
    {
        $keys = explode('.', $key);
        $config = &$this->config;

        foreach ($keys as $i => $k) {
            if ($i === count($keys) - 1) {
                $config[$k] = $value;
            } else {
                if (!isset($config[$k]) || !is_array($config[$k])) {
                    $config[$k] = [];
                }
                $config = &$config[$k];
            }
        }
    }

    /**
     * Check if a configuration key exists
     */
    public function has(string $key): bool
    {
        return $this->get($key) !== null;
    }

    /**
     * Get all configuration
     */
    public function all(): array
    {
        return $this->config;
    }
}