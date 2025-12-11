<?php
/**
 * Database Configuration
 * Works on Windows, macOS, Linux, and deployed servers
 */

// Load app configuration
require_once __DIR__ . '/../config/app.php';

// Database credentials - modify these for your environment
$DB_HOST = 'localhost';
$DB_NAME = 'central_cmi';
$DB_USER = 'root';
$DB_PASS = '';
$DB_PORT = 3306;

// Optional: Load from environment file if exists
$envFile = __DIR__ . '/../config/env.php';
if (file_exists($envFile)) {
    $env = require $envFile;
    $DB_HOST = $env['db_host'] ?? $DB_HOST;
    $DB_NAME = $env['db_name'] ?? $DB_NAME;
    $DB_USER = $env['db_user'] ?? $DB_USER;
    $DB_PASS = $env['db_pass'] ?? $DB_PASS;
    $DB_PORT = $env['db_port'] ?? $DB_PORT;
}

$pdo = null;

try {
    // PDO options
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    
    // Try TCP connection first (works on all platforms)
    try {
        $dsn = "mysql:host={$DB_HOST};port={$DB_PORT};dbname={$DB_NAME};charset=utf8mb4";
        $pdo = new PDO($dsn, $DB_USER, $DB_PASS, $options);
    } catch (PDOException $tcpError) {
        // If TCP fails on macOS/Linux, try Unix socket
        if (PHP_OS_FAMILY !== 'Windows') {
            $socketPaths = [
                '/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock', // macOS XAMPP
                '/var/run/mysqld/mysqld.sock',                          // Linux default
                '/tmp/mysql.sock',                                       // Alternative
                '/var/lib/mysql/mysql.sock',                            // CentOS/RHEL
            ];
            
            foreach ($socketPaths as $socket) {
                if (file_exists($socket)) {
                    try {
                        $dsn = "mysql:unix_socket={$socket};dbname={$DB_NAME};charset=utf8mb4";
                        $pdo = new PDO($dsn, $DB_USER, $DB_PASS, $options);
                        break;
                    } catch (PDOException $e) {
                        continue;
                    }
                }
            }
        }
        
        // If still no connection, throw original error
        if ($pdo === null) {
            throw $tcpError;
        }
    }
} catch (Throwable $e) {
    error_log('DB connection failed: ' . $e->getMessage());
    // Don't expose error details in production
    if (php_sapi_name() !== 'cli') {
        // Only show error page for web requests
        http_response_code(500);
        if (strpos($_SERVER['REQUEST_URI'] ?? '', '/api/') !== false) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Database connection failed']);
        } else {
            echo '<!DOCTYPE html><html><head><title>Error</title></head><body>';
            echo '<h1>Database Connection Error</h1>';
            echo '<p>Please check your database configuration.</p>';
            echo '</body></html>';
        }
        exit;
    }
}


