<?php

namespace Core;

/**
 * Logger Class
 * Simple file-based logging
 */

class Logger
{
    private string $logPath;

    public function __construct(string $logPath)
    {
        $this->logPath = rtrim($logPath, '/');
        
        // Create log directory if it doesn't exist
        if (!is_dir($this->logPath)) {
            mkdir($this->logPath, 0755, true);
        }
    }

    /**
     * Log info message
     */
    public function info(string $message, array $context = []): void
    {
        $this->log('INFO', $message, $context);
    }

    /**
     * Log warning message
     */
    public function warning(string $message, array $context = []): void
    {
        $this->log('WARNING', $message, $context);
    }

    /**
     * Log error message
     */
    public function error(string $message, array $context = []): void
    {
        $this->log('ERROR', $message, $context);
    }

    /**
     * Log debug message
     */
    public function debug(string $message, array $context = []): void
    {
        $this->log('DEBUG', $message, $context);
    }

    /**
     * Write log entry to file
     */
    private function log(string $level, string $message, array $context = []): void
    {
        $date = date('Y-m-d H:i:s');
        $logMessage = "[{$date}] [{$level}] {$message}";
        
        if (!empty($context)) {
            $logMessage .= ' ' . json_encode($context);
        }

        $logFile = $this->logPath . '/' . date('Y-m-d') . '.log';
        file_put_contents($logFile, $logMessage . PHP_EOL, FILE_APPEND);
    }
}