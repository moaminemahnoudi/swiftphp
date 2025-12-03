<?php

declare(strict_types=1);

namespace SwiftPHP\Console\Commands;

class NewCommand
{
    public function execute(array $args): void
    {
        if (empty($args[0])) {
            echo "\033[31mError: Application name is required\033[0m\n";
            echo "Usage: swiftphp new <app-name>\n";
            return;
        }

        $appName = $args[0];
        $appPath = getcwd() . DIRECTORY_SEPARATOR . $appName;

        if (is_dir($appPath)) {
            echo "\033[31mError: Directory '$appName' already exists\033[0m\n";
            return;
        }

        echo "\033[32mCreating SwiftPHP application '$appName'...\033[0m\n";

        $this->createDirectoryStructure($appPath);
        $this->copyFrameworkFiles($appPath);
        $this->createComposerJson($appPath, $appName);
        $this->createEnvFile($appPath);

        echo "\033[32mApplication '$appName' created successfully!\033[0m\n";
        echo "\nNext steps:\n";
        echo "  cd $appName\n";
        echo "  composer install\n";
        echo "  swiftphp serve\n";
    }

    private function createDirectoryStructure(string $path): void
    {
        $directories = [
            'app/Controllers',
            'app/Models',
            'config',
            'public',
            'resources/views/layouts',
            'resources/views/components',
            'src/Core',
            'src/Database',
            'src/Security',
            'src/View',
            'src/Console',
            'src/Console/Commands',
            'storage/views',
            'database/migrations',
            'bin'
        ];

        foreach ($directories as $dir) {
            mkdir($path . DIRECTORY_SEPARATOR . $dir, 0755, true);
        }
    }

    private function copyFrameworkFiles(string $path): void
    {
        $frameworkPath = __DIR__ . '/../../..';

        // Copy core files
        $this->copyDirectory($frameworkPath . '/src', $path . '/src');
        $this->copyDirectory($frameworkPath . '/config', $path . '/config');
        $this->copyDirectory($frameworkPath . '/resources', $path . '/resources');
        $this->copyDirectory($frameworkPath . '/bin', $path . '/bin');

        // Copy public/index.php
        copy($frameworkPath . '/public/index.php', $path . '/public/index.php');

        // Copy example files
        if (file_exists($frameworkPath . '/app/Controllers/UserController.php')) {
            copy($frameworkPath . '/app/Controllers/UserController.php', $path . '/app/Controllers/UserController.php');
        }
        if (file_exists($frameworkPath . '/app/Models/User.php')) {
            copy($frameworkPath . '/app/Models/User.php', $path . '/app/Models/User.php');
        }
    }

    private function copyDirectory(string $src, string $dst): void
    {
        if (!is_dir($src)) {
            return;
        }

        if (!is_dir($dst)) {
            mkdir($dst, 0755, true);
        }

        $files = scandir($src);
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                $srcFile = $src . DIRECTORY_SEPARATOR . $file;
                $dstFile = $dst . DIRECTORY_SEPARATOR . $file;

                if (is_dir($srcFile)) {
                    $this->copyDirectory($srcFile, $dstFile);
                } else {
                    copy($srcFile, $dstFile);
                }
            }
        }
    }

    private function createComposerJson(string $path, string $name): void
    {
        $composer = [
            'name' => strtolower($name) . '/app',
            'description' => 'A SwiftPHP application',
            'type' => 'project',
            'require' => [
                'php' => '>=8.1'
            ],
            'autoload' => [
                'psr-4' => [
                    'SwiftPHP\\' => 'src/',
                    'App\\' => 'app/'
                ]
            ],
            'scripts' => [
                'serve' => 'php -S localhost:8000 -t public',
                'post-create-project-cmd' => [
                    'php -r "copy(\'.env.example\', \'.env\');"'
                ]
            ],
            'bin' => ['bin/swiftphp']
        ];

        file_put_contents($path . '/composer.json', json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }

    private function createEnvFile(string $path): void
    {
        $env = "APP_NAME=$name
APP_ENV=development
APP_DEBUG=true
APP_URL=http://localhost:8000
APP_KEY=" . bin2hex(random_bytes(16)) . "

DB_DRIVER=sqlite
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=database.sqlite
DB_USERNAME=
DB_PASSWORD=
";
        file_put_contents($path . '/.env.example', $env);
    }
}
