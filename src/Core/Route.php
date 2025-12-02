<?php

namespace SwiftPHP\Core;

class Route
{
    private static Router $router;
    private static array $groupStack = [];

    public static function setRouter(Router $router): void
    {
        self::$router = $router;
    }

    public static function get(string $path, $handler): void
    {
        self::addRoute('GET', $path, $handler);
    }

    public static function post(string $path, $handler): void
    {
        self::addRoute('POST', $path, $handler);
    }

    public static function put(string $path, $handler): void
    {
        self::addRoute('PUT', $path, $handler);
    }

    public static function delete(string $path, $handler): void
    {
        self::addRoute('DELETE', $path, $handler);
    }

    public static function patch(string $path, $handler): void
    {
        self::addRoute('PATCH', $path, $handler);
    }

    public static function any(string $path, $handler): void
    {
        foreach (['GET', 'POST', 'PUT', 'DELETE', 'PATCH'] as $method) {
            self::addRoute($method, $path, $handler);
        }
    }

    public static function group(array $attributes, callable $callback): void
    {
        self::$groupStack[] = $attributes;
        $callback();
        array_pop(self::$groupStack);
    }

    public static function prefix(string $prefix): RouteGroup
    {
        return new RouteGroup(['prefix' => $prefix]);
    }

    public static function middleware(string|array $middleware): RouteGroup
    {
        return new RouteGroup(['middleware' => (array) $middleware]);
    }

    private static function addRoute(string $method, string $path, $handler): void
    {
        $groupAttributes = self::mergeGroupAttributes();
        
        if (!empty($groupAttributes['prefix'])) {
            $path = '/' . trim($groupAttributes['prefix'], '/') . '/' . trim($path, '/');
        }

        $middleware = $groupAttributes['middleware'] ?? [];
        
        self::$router->addRoute($method, $path, $handler, $middleware);
    }

    private static function mergeGroupAttributes(): array
    {
        $merged = [
            'prefix' => '',
            'middleware' => []
        ];

        foreach (self::$groupStack as $group) {
            if (isset($group['prefix'])) {
                $merged['prefix'] .= '/' . trim($group['prefix'], '/');
            }
            if (isset($group['middleware'])) {
                $merged['middleware'] = array_merge($merged['middleware'], (array) $group['middleware']);
            }
        }

        return $merged;
    }
}

class RouteGroup
{
    private array $attributes;

    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;
    }

    public function prefix(string $prefix): self
    {
        $this->attributes['prefix'] = $prefix;
        return $this;
    }

    public function middleware(string|array $middleware): self
    {
        $this->attributes['middleware'] = array_merge(
            $this->attributes['middleware'] ?? [],
            (array) $middleware
        );
        return $this;
    }

    public function group(callable $callback): void
    {
        Route::group($this->attributes, $callback);
    }
}
