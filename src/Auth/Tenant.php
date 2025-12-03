<?php

declare(strict_types=1);

namespace SwiftPHP\Auth;

class Tenant
{
    private static ?int $currentTenantId = null;

    public static function set(int $tenantId): void
    {
        self::$currentTenantId = $tenantId;

        if (!isset($_SESSION)) {
            session_start();
        }

        $_SESSION['tenant_id'] = $tenantId;
    }

    public static function get(): ?int
    {
        if (self::$currentTenantId !== null) {
            return self::$currentTenantId;
        }

        if (!isset($_SESSION)) {
            session_start();
        }

        self::$currentTenantId = $_SESSION['tenant_id'] ?? null;
        return self::$currentTenantId;
    }

    public static function clear(): void
    {
        self::$currentTenantId = null;

        if (!isset($_SESSION)) {
            session_start();
        }

        unset($_SESSION['tenant_id']);
    }

    public static function scope(callable $callback)
    {
        return function () use ($callback) {
            $tenantId = self::get();

            if ($tenantId === null) {
                return $callback();
            }

            // Apply tenant filter to query
            return $callback()->where('tenant_id', '=', $tenantId);
        };
    }

    public static function check(): bool
    {
        return self::get() !== null;
    }
}
