<?php

declare(strict_types=1);

// Helper Functions for SwiftPHP Framework

use SwiftPHP\Http\Response;
use SwiftPHP\Support\Collection;
use SwiftPHP\Support\Env;

if (!function_exists('env')) {
    function env(string $key, $default = null)
    {
        return Env::get($key, $default);
    }
}

if (!function_exists('collect')) {
    function collect(array $items = []): Collection
    {
        return new Collection($items);
    }
}

if (!function_exists('response')) {
    function response(string $content = '', int $status = 200): Response
    {
        return Response::make($content, $status);
    }
}

if (!function_exists('json')) {
    function json(array $data, int $status = 200): Response
    {
        return Response::json($data, $status);
    }
}

if (!function_exists('view')) {
    function view(string $view, array $data = []): Response
    {
        return Response::view($view, $data);
    }
}

if (!function_exists('redirect')) {
    function redirect(string $url, int $status = 302): Response
    {
        return Response::redirect($url, $status);
    }
}

if (!function_exists('dd')) {
    function dd(...$vars): void
    {
        foreach ($vars as $var) {
            echo '<pre>';
            var_dump($var);
            echo '</pre>';
        }
        die();
    }
}

if (!function_exists('dump')) {
    function dump(...$vars): void
    {
        foreach ($vars as $var) {
            echo '<pre>';
            var_dump($var);
            echo '</pre>';
        }
    }
}

// Authentication Helpers
if (!function_exists('auth')) {
    function auth()
    {
        return new \SwiftPHP\Auth\Auth();
    }
}

if (!function_exists('user')) {
    function user()
    {
        return \SwiftPHP\Auth\Auth::user();
    }
}

if (!function_exists('can')) {
    function can(string $permission): bool
    {
        return \SwiftPHP\Auth\Auth::can($permission);
    }
}

if (!function_exists('hasRole')) {
    function hasRole(string|array $roles): bool
    {
        return \SwiftPHP\Auth\Auth::hasRole($roles);
    }
}

// Export Helper
if (!function_exists('export')) {
    function export(array|\SwiftPHP\Support\Collection $data): \SwiftPHP\Export\Exporter
    {
        return \SwiftPHP\Export\Exporter::make($data);
    }
}
