<?php

declare(strict_types=1);

namespace SwiftPHP\Core;

use SwiftPHP\Error\ErrorHandler;

class Application
{
    private Router $router;
    private Container $container;
    private array $middleware = [];

    public function __construct()
    {
        // Register error handler with AI hints
        ErrorHandler::register();

        $this->container = new Container();
        $this->router = new Router($this->container);
        $this->loadConfig();
    }

    public function run(): void
    {
        try {
            Route::setRouter($this->router);
            $response = $this->router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
            echo $response;
        } catch (\Exception $e) {
            http_response_code(500);
            echo $this->renderError($e);
        }
    }

    public function get(string $path, $handler): void
    {
        $this->router->addRoute('GET', $path, $handler, []);
    }

    public function post(string $path, $handler): void
    {
        $this->router->addRoute('POST', $path, $handler, []);
    }

    public function put(string $path, $handler): void
    {
        $this->router->addRoute('PUT', $path, $handler, []);
    }

    public function delete(string $path, $handler): void
    {
        $this->router->addRoute('DELETE', $path, $handler, []);
    }

    public function getRouter(): Router
    {
        return $this->router;
    }

    public function getContainer(): Container
    {
        return $this->container;
    }

    public function middleware(string $middleware): self
    {
        $this->middleware[] = $middleware;
        return $this;
    }

    private function loadConfig(): void
    {
        $configPath = __DIR__ . '/../../config/app.php';
        if (file_exists($configPath)) {
            $config = require $configPath;
            foreach ($config as $key => $value) {
                $_ENV[$key] = $value;
            }
        }
    }

    private function renderError(\Exception $e): string
    {
        if ($_ENV['APP_DEBUG'] ?? false) {
            return "<h1>Error: {$e->getMessage()}</h1><pre>{$e->getTraceAsString()}</pre>";
        }
        return "<h1>Something went wrong</h1>";
    }
}
