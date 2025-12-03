<?php

declare(strict_types=1);

namespace SwiftPHP\Console\Commands;

class MakeMigrationCommand
{
    public function execute(array $args): void
    {
        if (empty($args[0])) {
            echo "\033[31mError: Migration name is required\033[0m\n";
            echo "Usage: swiftphp make:migration <migration_name>\n";
            return;
        }

        $migrationName = $args[0];
        $timestamp = date('Y_m_d_His');
        $fileName = $timestamp . '_' . $migrationName . '.php';
        $migrationPath = "database/migrations/$fileName";

        if (!is_dir('database/migrations')) {
            mkdir('database/migrations', 0755, true);
        }

        $template = $this->getMigrationTemplate($migrationName);
        file_put_contents($migrationPath, $template);

        echo "\033[32mMigration '$fileName' created successfully!\033[0m\n";
        echo "Location: $migrationPath\n";
    }

    private function getMigrationTemplate(string $name): string
    {
        $className = $this->getMigrationClassName($name);

        return "<?php

class $className
{
    public function up(): void
    {
        // Add your migration logic here
        // Example:
        // \$db = \\SwiftPHP\\Database\\Database::getInstance();
        // \$db->query(\"
        //     CREATE TABLE example (
        //         id INTEGER PRIMARY KEY AUTOINCREMENT,
        //         name VARCHAR(255) NOT NULL,
        //         created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        //     )
        // \")->get();
    }

    public function down(): void
    {
        // Add rollback logic here
        // Example:
        // \$db = \\SwiftPHP\\Database\\Database::getInstance();
        // \$db->query(\"DROP TABLE IF EXISTS example\")->get();
    }
}
";
    }

    private function getMigrationClassName(string $name): string
    {
        $parts = explode('_', $name);
        return implode('', array_map('ucfirst', $parts));
    }
}
