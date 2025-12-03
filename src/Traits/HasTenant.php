<?php

declare(strict_types=1);

namespace SwiftPHP\Traits;

use SwiftPHP\Auth\Tenant;

trait HasTenant
{
    public static function all(): array
    {
        $instance = new static();
        $query = \SwiftPHP\Database\Database::getInstance()
            ->table($instance->table);

        $tenantId = Tenant::get();
        if ($tenantId !== null) {
            $query->where('tenant_id', '=', $tenantId);
        }

        $results = $query->get();
        return array_map(fn ($row) => new static($row), $results);
    }

    public static function find(int $id): ?static
    {
        $instance = new static();
        $query = \SwiftPHP\Database\Database::getInstance()
            ->table($instance->table)
            ->where($instance->primaryKey, '=', $id);

        $tenantId = Tenant::get();
        if ($tenantId !== null) {
            $query->where('tenant_id', '=', $tenantId);
        }

        $result = $query->first();
        return $result ? new static($result) : null;
    }

    public static function where(string $column, string $operator, $value): array
    {
        $instance = new static();
        $query = \SwiftPHP\Database\Database::getInstance()
            ->table($instance->table)
            ->where($column, $operator, $value);

        $tenantId = Tenant::get();
        if ($tenantId !== null) {
            $query->where('tenant_id', '=', $tenantId);
        }

        $results = $query->get();
        return array_map(fn ($row) => new static($row), $results);
    }

    public function save(): bool
    {
        // Auto-assign tenant_id on create
        if (!isset($this->attributes[$this->primaryKey])) {
            $tenantId = Tenant::get();
            if ($tenantId !== null && !isset($this->attributes['tenant_id'])) {
                $this->attributes['tenant_id'] = $tenantId;
            }
        }

        return parent::save();
    }
}
