<?php

declare(strict_types=1);

namespace SwiftPHP\Traits;

trait HasRoles
{
    public function hasRole(string|array $roles): bool
    {
        $roles = is_array($roles) ? $roles : [$roles];
        $userRole = $this->attributes['role'] ?? 'user';

        return in_array($userRole, $roles);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('superadmin');
    }

    public function can(string $permission): bool
    {
        $permissions = $this->attributes['permissions'] ?? [];

        if (is_string($permissions)) {
            $permissions = json_decode($permissions, true) ?? [];
        }

        return in_array($permission, $permissions);
    }

    public function grantPermission(string $permission): void
    {
        $permissions = $this->attributes['permissions'] ?? [];

        if (is_string($permissions)) {
            $permissions = json_decode($permissions, true) ?? [];
        }

        if (!in_array($permission, $permissions)) {
            $permissions[] = $permission;
            $this->attributes['permissions'] = json_encode($permissions);
        }
    }

    public function revokePermission(string $permission): void
    {
        $permissions = $this->attributes['permissions'] ?? [];

        if (is_string($permissions)) {
            $permissions = json_decode($permissions, true) ?? [];
        }

        $permissions = array_filter($permissions, fn ($p) => $p !== $permission);
        $this->attributes['permissions'] = json_encode(array_values($permissions));
    }
}
