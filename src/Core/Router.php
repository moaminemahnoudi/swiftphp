<?php

declare(strict_types=1);

namespace SwiftPHP\Core;

class Router
{
    private array $routes = [];
    private Container $container;
    private array $middleware = [];

    public function __construct(Container $container)
    {
        $this->container = $container;
        Route::setRouter($this); // initialize static reference safely
    }

    public function addRoute(string $method, string $path, $handler, array $middleware = []): void
    {
        $this->routes[$method][$path] = [
            'handler' => $handler,
            'middleware' => $middleware
        ];
    }

    public function dispatch(string $method, string $uri): string
    {
        $path = parse_url($uri, PHP_URL_PATH);

        if (isset($this->routes[$method][$path])) {
            $route = $this->routes[$method][$path];
            return $this->executeRoute($route, []);
        }

        // Check for dynamic routes
        foreach ($this->routes[$method] ?? [] as $routePath => $route) {
            if ($params = $this->matchRoute($routePath, $path)) {
                return $this->executeRoute($route, $params);
            }
        }

        http_response_code(404);
        return "404 - Not Found";
    }

    private function executeRoute(array $route, array $params): string
    {
        $response = $this->callHandler($route['handler'], $params);

        // Apply middleware
        foreach (array_reverse($route['middleware']) as $middlewareClass) {
            $middleware = $this->container->resolve($middlewareClass);
            if (method_exists($middleware, 'handle')) {
                $response = $middleware->handle($response);
            }
        }

        return $response;
    }

    private function matchRoute(string $route, string $path): array|false
    {
        $routePattern = preg_replace('/\{([^}]+)\}/', '([^/]+)', $route);
        $routePattern = '#^' . $routePattern . '$#';

        if (preg_match($routePattern, $path, $matches)) {
            array_shift($matches);
            return $matches;
        }

        return false;
    }

    private function callHandler($handler, array $params = []): string
    {
        if (is_callable($handler)) {
            return call_user_func_array($handler, $params);
        }

        if (is_string($handler) && str_contains($handler, '@')) {
            [$controller, $method] = explode('@', $handler);
            $controllerInstance = $this->container->resolve($controller);
            return call_user_func_array([$controllerInstance, $method], $params);
        }

        throw new \Exception("Invalid handler");
    }
}
