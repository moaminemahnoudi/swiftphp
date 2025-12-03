<?php

declare(strict_types=1);

namespace SwiftPHP\Console\Commands;

class ServeCommand
{
    public function execute(array $args): void
    {
        $host = $args[0] ?? 'localhost';
        $port = $args[1] ?? '8000';

        if (!file_exists('public/index.php')) {
            echo "\033[31mError: Not a SwiftPHP application. Run this command from the root directory.\033[0m\n";
            return;
        }

        echo "\033[32mSwiftPHP development server started\033[0m\n";
        echo "Server running at: \033[36mhttp://$host:$port\033[0m\n";
        echo "Press Ctrl+C to stop the server\n\n";

        $command = "php -S $host:$port -t public";

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            pclose(popen("start /B $command", "r"));
        } else {
            exec($command);
        }
    }
}
