<?php

declare(strict_types=1);

namespace SwiftPHP\Console\Commands;

class MakeControllerCommand
{
    public function execute(array $args): void
    {
        if (empty($args[0])) {
            echo "\033[31mError: Controller name is required\033[0m\n";
            echo "Usage: swiftphp make:controller <ControllerName>\n";
            return;
        }

        $controllerName = $args[0];
        if (!str_ends_with($controllerName, 'Controller')) {
            $controllerName .= 'Controller';
        }

        $controllerPath = "app/Controllers/$controllerName.php";

        if (file_exists($controllerPath)) {
            echo "\033[31mError: Controller '$controllerName' already exists\033[0m\n";
            return;
        }

        $template = $this->getControllerTemplate($controllerName);

        if (!is_dir('app/Controllers')) {
            mkdir('app/Controllers', 0755, true);
        }

        file_put_contents($controllerPath, $template);

        echo "\033[32mController '$controllerName' created successfully!\033[0m\n";
        echo "Location: $controllerPath\n";
    }

    private function getControllerTemplate(string $name): string
    {
        return "<?php

namespace App\\Controllers;

use SwiftPHP\\Core\\Controller;

class $name extends Controller
{
    public function index(): string
    {
        return \$this->view('index');
    }

    public function show(string \$id): string
    {
        return \$this->view('show', ['id' => \$id]);
    }

    public function create(): string
    {
        return \$this->view('create');
    }

    public function store(): string
    {
        // Handle form submission
        \$this->redirect('/');
    }

    public function edit(string \$id): string
    {
        return \$this->view('edit', ['id' => \$id]);
    }

    public function update(string \$id): string
    {
        // Handle update
        \$this->redirect('/');
    }

    public function destroy(string \$id): string
    {
        // Handle deletion
        \$this->redirect('/');
    }
}
";
    }
}
