<?php

declare(strict_types=1);

namespace SwiftPHP\Database;

class Database
{
    private \PDO $pdo;
    private static ?Database $instance = null;

    private function __construct(array $config)
    {
        $dsn = $this->buildDsn($config);
        $this->pdo = new \PDO(
            $dsn,
            $config['username'] ?? null,
            $config['password'] ?? null,
            [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES => false
            ]
        );
    }

    public static function getInstance(array $config = null): Database
    {
        if (self::$instance === null) {
            if ($config === null) {
                $config = require __DIR__ . '/../../config/database.php';
            }
            self::$instance = new self($config);
        }
        return self::$instance;
    }

    public function query(string $sql, array $params = []): QueryBuilder
    {
        return new QueryBuilder($this->pdo, $sql, $params);
    }

    public function table(string $table): QueryBuilder
    {
        return new QueryBuilder($this->pdo, table: $table);
    }

    public function beginTransaction(): bool
    {
        return $this->pdo->beginTransaction();
    }

    public function commit(): bool
    {
        return $this->pdo->commit();
    }

    public function rollback(): bool
    {
        return $this->pdo->rollback();
    }

    private function buildDsn(array $config): string
    {
        return match ($config['driver']) {
            'mysql' => "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset=utf8mb4",
            'pgsql' => "pgsql:host={$config['host']};port={$config['port']};dbname={$config['database']}",
            'sqlite' => "sqlite:{$config['database']}",
            default => throw new \Exception("Unsupported database driver: {$config['driver']}")
        };
    }
}
