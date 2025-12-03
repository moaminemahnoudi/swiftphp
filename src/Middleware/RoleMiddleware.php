<?php

declare(strict_types=1);

namespace SwiftPHP\Middleware;

use SwiftPHP\Auth\Auth;

class RoleMiddleware implements Middleware
{
    private array $roles;

    public function __construct(string|array $roles = [])
    {
        $this->roles = is_array($roles) ? $roles : [$roles];
    }

    public function handle($response): mixed
    {
        if (!Auth::check()) {
            http_response_code(401);
            return json(['error' => 'Unauthorized - Please login'], 401);
        }

        if (!empty($this->roles) && !Auth::hasRole($this->roles)) {
            http_response_code(403);
            return json(['error' => 'Forbidden - Insufficient permissions'], 403);
        }

        return $response;
    }

    public static function admin(): self
    {
        return new self('admin');
    }

    public static function superAdmin(): self
    {
        return new self('superadmin');
    }

    public static function any(array $roles): self
    {
        return new self($roles);
    }
}
