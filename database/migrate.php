<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/Core/Helpers.php';
require_once __DIR__ . '/../app/Core/Database.php';

use App\Core\Database;

$isCli = (php_sapi_name() === 'cli');

function logMsg(string $msg, string $type = 'info'): void {
    $colors = [
        'info' => "\033[36m",
        'success' => "\033[32m",
        'warning' => "\033[33m",
        'error' => "\033[31m",
        'reset' => "\033[0m",
    ];
    $color = $colors[$type] ?? $colors['info'];
    $reset = $colors['reset'];
    echo "{$color}[SIDRA] {$msg}{$reset}\n";
}

logMsg("Starting Sidra Database Migration...", "info");

// 1. Load active .env if present
$envFile = dirname(__DIR__) . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }
        if (str_contains($line, '=')) {
            [$k, $v] = explode('=', $line, 2);
            $k = trim($k);
            $v = trim($v, " \t\n\r\0\x0B\"'");
            $_ENV[$k] = $v;
            putenv("{$k}={$v}");
        }
    }
}

$driver = $_ENV['DB_CONNECTION'] ?? 'sqlite';
$seed = in_array('--seed', $argv ?? [], true) || in_array('-s', $argv ?? [], true);

try {
    if ($driver === 'mysql') {
        logMsg("Connecting to MySQL host: " . ($_ENV['DB_HOST'] ?? '127.0.0.1'), "info");
        
        // Connect to server without db first to ensure db exists
        $host = $_ENV['DB_HOST'] ?? '127.0.0.1';
        $port = $_ENV['DB_PORT'] ?? '3306';
        $dbName = $_ENV['DB_DATABASE'] ?? 'sidra_ticketing';
        $user = $_ENV['DB_USERNAME'] ?? 'root';
        $pass = $_ENV['DB_PASSWORD'] ?? '';

        $pdo = new PDO("mysql:host={$host};port={$port};charset=utf8mb4", $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);

        logMsg("Ensuring database `{$dbName}` exists...", "info");
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $pdo->exec("USE `{$dbName}`");

        logMsg("Executing MySQL schema (database/schema.sql)...", "info");
        $schemaSql = file_get_contents(__DIR__ . '/schema.sql');
        $pdo->exec($schemaSql);
        logMsg("MySQL Schema migrated successfully!", "success");

        if ($seed) {
            logMsg("Executing Seeds (database/seeds.sql)...", "info");
            $seedSql = file_get_contents(__DIR__ . '/seeds.sql');
            $pdo->exec($seedSql);
            logMsg("Database seeded successfully with initial staff, sample events, and settings!", "success");
        }
    } else {
        logMsg("Using SQLite database driver (Zero-Config local mode)...", "info");
        $sqliteRelative = $_ENV['DB_SQLITE_PATH'] ?? 'storage/database/sidra.sqlite';
        $sqlitePath = dirname(__DIR__) . '/' . $sqliteRelative;
        $dir = dirname($sqlitePath);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $pdo = new PDO("sqlite:{$sqlitePath}", null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);
        $pdo->exec("PRAGMA foreign_keys = OFF;");

        logMsg("Executing SQLite schema (database/schema_sqlite.sql)...", "info");
        $schemaSql = file_get_contents(__DIR__ . '/schema_sqlite.sql');
        $pdo->exec($schemaSql);
        logMsg("SQLite Schema migrated successfully!", "success");

        if ($seed) {
            logMsg("Executing Seeds (database/seeds.sql)...", "info");
            $seedSql = file_get_contents(__DIR__ . '/seeds.sql');
            $pdo->exec($seedSql);
            logMsg("Database seeded successfully with initial staff, sample events, and settings!", "success");
        }

        $pdo->exec("PRAGMA foreign_keys = ON;");
    }

    logMsg("============================================================", "success");
    logMsg("Migration Complete! Your Sidra database is ready.", "success");
    logMsg("Default Super Admin Login:  admin@sidra.test    | Pass: Password123!", "info");
    logMsg("Default Event Manager:      manager@sidra.test  | Pass: Password123!", "info");
    logMsg("Default Finance Manager:    finance@sidra.test  | Pass: Password123!", "info");
    logMsg("Default Gate Staff:         gate@sidra.test     | Pass: Password123!", "info");
    logMsg("Default Customer Account:   customer@sidra.test | Pass: Password123!", "info");
    logMsg("============================================================", "success");
} catch (Throwable $e) {
    logMsg("Migration Failed: " . $e->getMessage(), "error");
    exit(1);
}
