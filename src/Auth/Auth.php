<?php

declare(strict_types=1);

namespace SwiftPHP\Auth;

use SwiftPHP\Core\Model;
use SwiftPHP\Security\Security;

class Auth
{
    private static ?Model $user = null;
    private static string $userModel = 'App\\Models\\User';

    public static function attempt(string $email, string $password): bool
    {
        if (!isset($_SESSION)) {
            session_start();
        }

        $userClass = self::$userModel;
        $users = $userClass::where('email', '=', $email);

        if (empty($users)) {
            return false;
        }

        $user = $users[0];

        if (Security::verifyPassword($password, $user->password)) {
            $_SESSION['user_id'] = $user->id;
            $_SESSION['user_email'] = $user->email;
            $_SESSION['user_role'] = $user->role ?? 'user';
            $_SESSION['tenant_id'] = $user->tenant_id ?? null;
            self::$user = $user;
            return true;
        }

        return false;
    }

    public static function login(Model $user): void
    {
        if (!isset($_SESSION)) {
            session_start();
        }

        $_SESSION['user_id'] = $user->id;
        $_SESSION['user_email'] = $user->email;
        $_SESSION['user_role'] = $user->role ?? 'user';
        $_SESSION['tenant_id'] = $user->tenant_id ?? null;
        self::$user = $user;
    }

    public static function logout(): void
    {
        if (!isset($_SESSION)) {
            session_start();
        }

        unset($_SESSION['user_id']);
        unset($_SESSION['user_email']);
        unset($_SESSION['user_role']);
        unset($_SESSION['tenant_id']);
        self::$user = null;
        session_destroy();
    }

    public static function check(): bool
    {
        if (!isset($_SESSION)) {
            session_start();
        }

        return isset($_SESSION['user_id']);
    }

    public static function guest(): bool
    {
        return !self::check();
    }

    public static function user(): ?Model
    {
        if (!self::check()) {
            return null;
        }

        if (self::$user === null) {
            $userClass = self::$userModel;
            self::$user = $userClass::find($_SESSION['user_id']);
        }

        return self::$user;
    }

    public static function id(): ?int
    {
        if (!isset($_SESSION)) {
            session_start();
        }

        return $_SESSION['user_id'] ?? null;
    }

    public static function email(): ?string
    {
        if (!isset($_SESSION)) {
            session_start();
        }

        return $_SESSION['user_email'] ?? null;
    }

    public static function role(): ?string
    {
        if (!isset($_SESSION)) {
            session_start();
        }

        return $_SESSION['user_role'] ?? null;
    }

    public static function hasRole(string|array $roles): bool
    {
        if (!self::check()) {
            return false;
        }

        $userRole = self::role();
        $roles = is_array($roles) ? $roles : [$roles];

        return in_array($userRole, $roles);
    }

    public static function isAdmin(): bool
    {
        return self::hasRole('admin');
    }

    public static function isSuperAdmin(): bool
    {
        return self::hasRole('superadmin');
    }

    public static function can(string $permission): bool
    {
        if (!self::check()) {
            return false;
        }

        $user = self::user();

        if (!$user || !isset($user->permissions)) {
            return false;
        }

        $permissions = is_string($user->permissions)
            ? json_decode($user->permissions, true)
            : $user->permissions;

        return in_array($permission, $permissions ?? []);
    }

    public static function tenantId(): ?int
    {
        if (!isset($_SESSION)) {
            session_start();
        }

        return $_SESSION['tenant_id'] ?? null;
    }

    public static function setUserModel(string $model): void
    {
        self::$userModel = $model;
    }

    public static function register(array $data): ?Model
    {
        $userClass = self::$userModel;

        // Hash password
        if (isset($data['password'])) {
            $data['password'] = Security::hashPassword($data['password']);
        }

        // Set default role if not provided
        if (!isset($data['role'])) {
            $data['role'] = 'user';
        }

        $user = new $userClass($data);

        if ($user->save()) {
            self::login($user);
            return $user;
        }

        return null;
    }
}
