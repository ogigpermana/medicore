<?php

namespace Core;

use PDO;
use PDOException;

/**
 * Database Class
 * PDO wrapper for database operations
 */

class Database
{
    private ?PDO $connection = null;
    private array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Get PDO connection
     */
    public function connect(): PDO
    {
        if ($this->connection === null) {
            try {
                $dsn = sprintf(
                    '%s:host=%s;port=%s;dbname=%s;charset=%s',
                    $this->config['driver'] ?? 'mysql',
                    $this->config['host'] ?? 'localhost',
                    $this->config['port'] ?? '3306',
                    $this->config['database'] ?? '',
                    $this->config['charset'] ?? 'utf8mb4'
                );

                $this->connection = new PDO($dsn, $this->config['username'], $this->config['password'], [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } catch (PDOException $e) {
                throw new \Exception("Database connection failed: " . $e->getMessage());
            }
        }

        return $this->connection;
    }

    /**
     * Execute a SELECT query
     */
    public function query(string $sql, array $params = []): array
    {
        try {
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            throw new \Exception("Query failed: " . $e->getMessage());
        }
    }

    /**
     * Execute an INSERT/UPDATE/DELETE query
     */
    public function execute(string $sql, array $params = []): bool
    {
        try {
            $stmt = $this->connect()->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            throw new \Exception("Execute failed: " . $e->getMessage());
        }
    }

    /**
     * Fetch a single row
     */
    public function fetch(string $sql, array $params = []): ?array
    {
        $result = $this->query($sql, $params);
        return $result[0] ?? null;
    }

    /**
     * Insert data into table
     */
    public function insert(string $table, array $data): int
    {
        $columns = array_keys($data);
        $placeholders = array_fill(0, count($columns), '?');

        $sql = sprintf(
            "INSERT INTO %s (%s) VALUES (%s)",
            $table,
            implode(', ', $columns),
            implode(', ', $placeholders)
        );

        $this->execute($sql, array_values($data));
        return (int)$this->connect()->lastInsertId();
    }

    /**
     * Update data in table
     */
    public function update(string $table, array $data, string $where, array $whereParams = []): bool
    {
        if (empty($data)) {
            return true;
        }

        $setParts = [];
        foreach (array_keys($data) as $column) {
            $setParts[] = "{$column} = ?";
        }

        $sql = sprintf(
            "UPDATE %s SET %s WHERE %s",
            $table,
            implode(', ', $setParts),
            $where
        );

        return $this->execute($sql, array_merge(array_values($data), $whereParams));
    }

    /**
     * Delete data from table
     */
    public function delete(string $table, string $where, array $params = []): bool
    {
        $sql = sprintf("DELETE FROM %s WHERE %s", $table, $where);
        return $this->execute($sql, $params);
    }

    /**
     * Execute callback within a transaction
     */
    public function transaction(callable $callback): mixed
    {
        try {
            $this->connect()->beginTransaction();
            $result = $callback();
            $this->connect()->commit();
            return $result;
        } catch (\Exception $e) {
            $this->connect()->rollBack();
            throw $e;
        }
    }

    /**
     * Get PDO connection
     */
    public function getConnection(): PDO
    {
        return $this->connect();
    }

    /**
     * Get PDO connection (alias)
     */
    public function getPdo(): PDO
    {
        return $this->connect();
    }
}