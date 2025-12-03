<?php

declare(strict_types=1);

namespace SwiftPHP\Core;

use SwiftPHP\Database\Database;

abstract class Model
{
    protected string $table;
    protected string $primaryKey = 'id';
    protected array $fillable = [];
    protected array $attributes = [];

    public function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;
        if (empty($this->table)) {
            $this->table = strtolower(basename(str_replace('\\', '/', static::class))) . 's';
        }
    }

    public static function find(int $id): ?static
    {
        $instance = new static();
        $result = Database::getInstance()
            ->table($instance->table)
            ->where($instance->primaryKey, '=', $id)
            ->first();

        return $result ? new static($result) : null;
    }

    public static function all(): array
    {
        $instance = new static();
        $results = Database::getInstance()
            ->table($instance->table)
            ->get();

        return array_map(fn ($row) => new static($row), $results);
    }

    public static function where(string $column, string $operator, $value): array
    {
        $instance = new static();
        $results = Database::getInstance()
            ->table($instance->table)
            ->where($column, $operator, $value)
            ->get();

        return array_map(fn ($row) => new static($row), $results);
    }

    public function hasMany(string $related, string $foreignKey = null, string $localKey = null): array
    {
        $foreignKey = $foreignKey ?? strtolower(basename(str_replace('\\', '/', static::class))) . '_id';
        $localKey = $localKey ?? $this->primaryKey;

        return $related::where($foreignKey, '=', $this->attributes[$localKey]);
    }

    public function belongsTo(string $related, string $foreignKey = null, string $ownerKey = null): ?Model
    {
        $foreignKey = $foreignKey ?? strtolower(basename(str_replace('\\', '/', $related))) . '_id';
        $ownerKey = $ownerKey ?? 'id';

        if (!isset($this->attributes[$foreignKey])) {
            return null;
        }

        return $related::find($this->attributes[$foreignKey]);
    }

    public function hasOne(string $related, string $foreignKey = null, string $localKey = null): ?Model
    {
        $foreignKey = $foreignKey ?? strtolower(basename(str_replace('\\', '/', static::class))) . '_id';
        $localKey = $localKey ?? $this->primaryKey;

        $results = $related::where($foreignKey, '=', $this->attributes[$localKey]);
        return $results[0] ?? null;
    }

    public static function with(array $relations): ModelQuery
    {
        return new ModelQuery(new static(), $relations);
    }

    public function save(): bool
    {
        $data = array_intersect_key($this->attributes, array_flip($this->fillable));

        if (isset($this->attributes[$this->primaryKey])) {
            return Database::getInstance()
                ->table($this->table)
                ->where($this->primaryKey, '=', $this->attributes[$this->primaryKey])
                ->update($data);
        } else {
            return Database::getInstance()
                ->table($this->table)
                ->insert($data);
        }
    }

    public function delete(): bool
    {
        if (!isset($this->attributes[$this->primaryKey])) {
            return false;
        }

        return Database::getInstance()
            ->table($this->table)
            ->where($this->primaryKey, '=', $this->attributes[$this->primaryKey])
            ->delete();
    }

    public function __get(string $name)
    {
        return $this->attributes[$name] ?? null;
    }

    public function __set(string $name, $value): void
    {
        $this->attributes[$name] = $value;
    }
}
