<?php

declare(strict_types=1);

namespace SwiftPHP\Console\Commands;

use SwiftPHP\Database\Database;

class MigrateCommand
{
    public function execute(array $args): void
    {
        if (!file_exists('database/migrations')) {
            echo "\033[31mError: Migrations directory not found\033[0m\n";
            return;
        }

        echo "\033[32mRunning migrations...\033[0m\n";

        try {
            $this->createMigrationsTable();
            $this->runMigrations();
            echo "\033[32mMigrations completed successfully!\033[0m\n";
        } catch (\Exception $e) {
            echo "\033[31mMigration failed: " . $e->getMessage() . "\033[0m\n";
        }
    }

    private function createMigrationsTable(): void
    {
        $db = Database::getInstance();
        $db->query("
            CREATE TABLE IF NOT EXISTS migrations (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                migration VARCHAR(255) NOT NULL,
                executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ")->get();
    }

    private function runMigrations(): void
    {
        $db = Database::getInstance();
        $executed = $db->query("SELECT migration FROM migrations")->get();
        $executedMigrations = array_column($executed, 'migration');

        $migrationFiles = glob('database/migrations/*.php');
        sort($migrationFiles);

        foreach ($migrationFiles as $file) {
            $migration = basename($file, '.php');

            if (in_array($migration, $executedMigrations)) {
                continue;
            }

            echo "Migrating: $migration\n";

            require_once $file;
            $className = $this->getMigrationClassName($migration);

            if (class_exists($className)) {
                $migrationInstance = new $className();
                $migrationInstance->up();

                $db->table('migrations')->insert(['migration' => $migration]);
                echo "\033[32mMigrated: $migration\033[0m\n";
            }
        }
    }

    private function getMigrationClassName(string $migration): string
    {
        $parts = explode('_', $migration);
        array_shift($parts); // Remove timestamp
        return implode('', array_map('ucfirst', $parts));
    }
}
