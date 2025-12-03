<?php

declare(strict_types=1);

namespace SwiftPHP\Console;

class Application
{
    private array $commands = [];

    public function __construct()
    {
        $this->registerCommands();
    }

    public function run(array $argv): void
    {
        $command = $argv[1] ?? 'help';
        $args = array_slice($argv, 2);

        if (!isset($this->commands[$command])) {
            $this->showHelp();
            return;
        }

        $commandClass = $this->commands[$command];
        $commandInstance = new $commandClass();
        $commandInstance->execute($args);
    }

    private function registerCommands(): void
    {
        $this->commands = [
            'new' => Commands\NewCommand::class,
            'serve' => Commands\ServeCommand::class,
            'migrate' => Commands\MigrateCommand::class,
            'make:controller' => Commands\MakeControllerCommand::class,
            'make:model' => Commands\MakeModelCommand::class,
            'make:migration' => Commands\MakeMigrationCommand::class,
            'generate' => Commands\GenerateCommand::class,
            'help' => Commands\HelpCommand::class,
        ];
    }

    private function showHelp(): void
    {
        echo "\033[32mSwiftPHP Framework\033[0m\n\n";
        echo "Usage:\n";
        echo "  swiftphp <command> [options]\n\n";
        echo "Available commands:\n";
        echo "  \033[33mnew <name>\033[0m          Create a new SwiftPHP application\n";
        echo "  \033[33mserve\033[0m               Start the development server\n";
        echo "  \033[33mmigrate\033[0m             Run database migrations\n";
        echo "  \033[33mgenerate\033[0m            🤖 AI-powered code generation from natural language\n";
        echo "  \033[33mmake:controller\033[0m     Create a new controller\n";
        echo "  \033[33mmake:model\033[0m          Create a new model\n";
        echo "  \033[33mmake:migration\033[0m      Create a new migration\n";
        echo "  \033[33mhelp\033[0m                Show this help message\n";
    }
}
