<?php

/**
 * Database Migration Runner
 * Run database migrations to set up the database schema
 */

// Load autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Get database configuration
$dbConfig = [
    'host' => getenv('DB_HOST') ?: 'localhost',
    'port' => getenv('DB_PORT') ?: '3306',
    'database' => getenv('DB_DATABASE') ?: 'medicore',
    'username' => getenv('DB_USERNAME') ?: 'root',
    'password' => getenv('DB_PASSWORD') ?: '',
    'charset' => 'utf8mb4'
];

try {
    // Connect to database
    $dsn = sprintf(
        'mysql:host=%s;port=%s;charset=%s',
        $dbConfig['host'],
        $dbConfig['port'],
        $dbConfig['charset']
    );

    $pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // Create database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbConfig['database']}`");
    $pdo->exec("USE `{$dbConfig['database']}`");

    echo "Database connected successfully.\n";

    // Create migrations table if it doesn't exist
    $pdo->exec("CREATE TABLE IF NOT EXISTS migrations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        migration VARCHAR(255) NOT NULL,
        batch INT NOT NULL,
        executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Get migration files
    $migrationFiles = glob(__DIR__ . '/migrations/*.php');
    natsort($migrationFiles); // Sort naturally

    echo "Found " . count($migrationFiles) . " migration files.\n";

    // Get executed migrations
    $executedMigrations = $pdo->query("SELECT migration FROM migrations")->fetchAll(PDO::FETCH_COLUMN);

    // Run new migrations
    foreach ($migrationFiles as $file) {
        $migrationName = basename($file, '.php');

        if (in_array($migrationName, $executedMigrations)) {
            echo "Skipping already executed migration: {$migrationName}\n";
            continue;
        }

        echo "Running migration: {$migrationName}\n";

        $migration = require $file;
        $migration->up($pdo);

        // Record migration
        $stmt = $pdo->prepare("INSERT INTO migrations (migration, batch) VALUES (?, ?)");
        $stmt->execute([$migrationName, 1]);

        echo "Migration completed: {$migrationName}\n";
    }

    echo "\nAll migrations completed successfully!\n";

} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
    exit(1);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}