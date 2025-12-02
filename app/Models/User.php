<?php

namespace App\Models;

use SwiftPHP\Core\Model;
use SwiftPHP\Traits\{HasRoles, HasTenant};

class User extends Model
{
    use HasRoles, HasTenant;

    protected string $table = 'users';
    protected array $fillable = ['name', 'email', 'password', 'role', 'tenant_id', 'permissions'];

    // Define user roles
    public const ROLE_USER = 'user';
    public const ROLE_ADMIN = 'admin';
    public const ROLE_SUPERADMIN = 'superadmin';

    // Hide password in JSON responses
    public function toArray(): array
    {
        $data = $this->attributes;
        unset($data['password']);
        return $data;
    }
}