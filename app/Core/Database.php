<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;
use Throwable;

class Database
{
    private static ?PDO $pdo = null;
    private static string $driver = 'mysql';
    private static int $transactionLevel = 0;

    public static function getConnection(): PDO
    {
        if (self::$pdo === null) {
            self::connect();
        }
        return self::$pdo;
    }

    public static function connect(): void
    {
        $driver = config('database.default', 'mysql');
        self::$driver = $driver;
        $dbConfig = config("database.connections.{$driver}");

        if (!$dbConfig) {
            throw new PDOException("Database driver '{$driver}' is not configured.");
        }

        try {
            if ($driver === 'sqlite') {
                $dbPath = $dbConfig['database'];
                $dir = dirname($dbPath);
                if (!is_dir($dir)) {
                    mkdir($dir, 0777, true);
                }
                self::$pdo = new PDO("sqlite:{$dbPath}", null, null, $dbConfig['options'] ?? []);
                self::$pdo->exec("PRAGMA foreign_keys = ON;");
            } else {
                $dsn = sprintf(
                    "mysql:host=%s;port=%d;dbname=%s;charset=%s",
                    $dbConfig['host'] ?? '127.0.0.1',
                    (int)($dbConfig['port'] ?? 3306),
                    $dbConfig['database'] ?? 'sidra_ticketing',
                    $dbConfig['charset'] ?? 'utf8mb4'
                );

                self::$pdo = new PDO(
                    $dsn,
                    $dbConfig['username'] ?? 'root',
                    $dbConfig['password'] ?? '',
                    $dbConfig['options'] ?? []
                );
            }
        } catch (PDOException $e) {
            throw new PDOException("Database connection failed: " . $e->getMessage(), (int)$e->getCode(), $e);
        }
    }

    public static function query(string $sql, array $params = []): \PDOStatement
    {
        $seen = [];
        $sql = preg_replace_callback('/:(\w+)/', function (array $match) use (&$params, &$seen): string {
            $name = $match[1];

            if (!array_key_exists($name, $params)) {
                return $match[0];
            }

            $seen[$name] = ($seen[$name] ?? 0) + 1;
            if ($seen[$name] === 1) {
                return $match[0];
            }

            $uniqueName = $name . '_' . $seen[$name];
            $params[$uniqueName] = $params[$name];
            return ':' . $uniqueName;
        }, $sql);

        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public static function fetch(string $sql, array $params = []): ?array
    {
        $stmt = self::query($sql, $params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public static function fetchAll(string $sql, array $params = []): array
    {
        $stmt = self::query($sql, $params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function insert(string $table, array $data): int
    {
        $fields = array_keys($data);
        $columns = implode('`, `', $fields);
        $placeholders = ':' . implode(', :', $fields);

        $sql = "INSERT INTO `{$table}` (`{$columns}`) VALUES ({$placeholders})";
        self::query($sql, $data);
        return (int)self::getConnection()->lastInsertId();
    }

    public static function update(string $table, array $data, string $where, array $whereParams = []): int
    {
        $fields = [];
        foreach (array_keys($data) as $field) {
            $fields[] = "`{$field}` = :set_{$field}";
        }
        $setClause = implode(', ', $fields);
        $sql = "UPDATE `{$table}` SET {$setClause} WHERE {$where}";

        $params = [];
        foreach ($data as $k => $v) {
            $params["set_{$k}"] = $v;
        }
        foreach ($whereParams as $k => $v) {
            $params[$k] = $v;
        }

        $stmt = self::query($sql, $params);
        return $stmt->rowCount();
    }

    public static function delete(string $table, string $where, array $params = []): int
    {
        $sql = "DELETE FROM `{$table}` WHERE {$where}";
        $stmt = self::query($sql, $params);
        return $stmt->rowCount();
    }

    public static function transaction(callable $callback): mixed
    {
        $pdo = self::getConnection();
        $isNested = $pdo->inTransaction();
        $savepoint = 'sidra_sp_' . (++self::$transactionLevel);

        if ($isNested) {
            $pdo->exec("SAVEPOINT {$savepoint}");
        } else {
            $pdo->beginTransaction();
        }

        try {
            $result = $callback($pdo);

            if ($isNested) {
                $pdo->exec("RELEASE SAVEPOINT {$savepoint}");
            } else {
                $pdo->commit();
            }

            self::$transactionLevel--;
            return $result;
        } catch (Throwable $e) {
            if ($isNested && $pdo->inTransaction()) {
                $pdo->exec("ROLLBACK TO SAVEPOINT {$savepoint}");
                $pdo->exec("RELEASE SAVEPOINT {$savepoint}");
            } elseif ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            self::$transactionLevel--;
            throw $e;
        }
    }

    public static function getDriver(): string
    {
        return self::$driver;
    }
}
