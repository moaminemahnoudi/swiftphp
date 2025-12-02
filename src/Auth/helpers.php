<?php

// Helper Functions for Authentication

use SwiftPHP\Auth\Auth;

if (!function_exists('auth')) {
    function auth(): Auth
    {
        return new Auth();
    }
}

if (!function_exists('user')) {
    function user()
    {
        return Auth::user();
    }
}

if (!function_exists('can')) {
    function can(string $permission): bool
    {
        return Auth::can($permission);
    }
}

if (!function_exists('hasRole')) {
    function hasRole(string|array $roles): bool
    {
        return Auth::hasRole($roles);
    }
}
