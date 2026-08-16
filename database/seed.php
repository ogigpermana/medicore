<?php

/**
 * Database Seeder Runner
 * Run database seeders to populate test data
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
        'mysql:host=%s;port=%s;dbname=%s;charset=%s',
        $dbConfig['host'],
        $dbConfig['port'],
        $dbConfig['database'],
        $dbConfig['charset']
    );

    $pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    echo "Database connected successfully.\n";

    // Get seeder files
    $seederFiles = glob(__DIR__ . '/seeders/*.php');
    natsort($seederFiles); // Sort naturally

    echo "Found " . count($seederFiles) . " seeder files.\n";

    // Run seeders
    foreach ($seederFiles as $file) {
        $seederName = basename($file, '.php');
        echo "Running seeder: {$seederName}\n";

        $seeder = require $file;
        $seeder->run($pdo);

        echo "Seeder completed: {$seederName}\n";
    }

    echo "\nAll seeders completed successfully!\n";

} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
    exit(1);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}