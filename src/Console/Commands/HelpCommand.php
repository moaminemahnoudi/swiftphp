<?php

declare(strict_types=1);

namespace SwiftPHP\Console\Commands;

class HelpCommand
{
    public function execute(array $args): void
    {
        echo "\033[32m
   ____         _ __ _   ____  _   _ ____  
  / ___|_      _(_)/ _| |  _ \\| | | |  _ \\ 
  \\___ \\ \\ /\\ / / | |_  | |_) | |_| | |_) |
   ___) \\ V  V /| |  _| |  __/|  _  |  __/ 
  |____/ \\_/\\_/ |_|_|   |_|   |_| |_|_|    
                                           
\033[0m";
        echo "SwiftPHP Framework - A lightweight, modern PHP framework\n\n";

        echo "\033[33mUsage:\033[0m\n";
        echo "  swiftphp <command> [options]\n\n";

        echo "\033[33mAvailable Commands:\033[0m\n\n";

        echo "\033[36mApplication:\033[0m\n";
        echo "  \033[32mnew <name>\033[0m              Create a new SwiftPHP application\n";
        echo "  \033[32mserve [host] [port]\033[0m     Start the development server (default: localhost:8000)\n\n";

        echo "\033[36mDatabase:\033[0m\n";
        echo "  \033[32mmigrate\033[0m                 Run database migrations\n";
        echo "  \033[32mmake:migration <name>\033[0m   Create a new migration file\n\n";

        echo "\033[36mCode Generation:\033[0m\n";
        echo "  \033[32mmake:controller <name>\033[0m  Create a new controller\n";
        echo "  \033[32mmake:model <name>\033[0m       Create a new model\n\n";

        echo "\033[36mHelp:\033[0m\n";
        echo "  \033[32mhelp\033[0m                    Show this help message\n\n";

        echo "\033[33mExamples:\033[0m\n";
        echo "  swiftphp new blog\n";
        echo "  swiftphp serve\n";
        echo "  swiftphp serve 127.0.0.1 3000\n";
        echo "  swiftphp make:controller PostController\n";
        echo "  swiftphp make:model Post\n";
        echo "  swiftphp make:migration create_posts_table\n";
        echo "  swiftphp migrate\n\n";
    }
}
