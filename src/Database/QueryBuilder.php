<?php

namespace SwiftPHP\Database;

class QueryBuilder
{
    private \PDO $pdo;
    private string $table = '';
    private array $wheres = [];
    private array $bindings = [];
    private string $sql = '';
    private array $params = [];

    public function __construct(\PDO $pdo, string $sql = '', array $params = [], string $table = '')
    {
        $this->pdo = $pdo;
        $this->sql = $sql;
        $this->params = $params;
        $this->table = $table;
    }

    public function select(array $columns = ['*']): self
    {
        $this->sql = 'SELECT ' . implode(', ', $columns) . ' FROM ' . $this->table;
        return $this;
    }

    public function where(string $column, string $operator, $value): self
    {
        $placeholder = ':' . $column . count($this->bindings);
        $this->wheres[] = "$column $operator $placeholder";
        $this->bindings[$placeholder] = $value;
        return $this;
    }

    public function insert(array $data): bool
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        $this->sql = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";
        $this->bindings = array_combine(
            array_map(fn($key) => ":$key", array_keys($data)),
            array_values($data)
        );
        return $this->execute();
    }

    public function update(array $data): bool
    {
        $sets = [];
        foreach ($data as $column => $value) {
            $placeholder = ":$column";
            $sets[] = "$column = $placeholder";
            $this->bindings[$placeholder] = $value;
        }
        $this->sql = "UPDATE {$this->table} SET " . implode(', ', $sets);
        $this->addWhereClause();
        return $this->execute();
    }

    public function delete(): bool
    {
        $this->sql = "DELETE FROM {$this->table}";
        $this->addWhereClause();
        return $this->execute();
    }

    public function get(): array
    {
        if (empty($this->sql)) {
            $this->select();
        }
        $this->addWhereClause();
        return $this->fetchAll();
    }

    public function first(): ?array
    {
        $results = $this->get();
        return $results[0] ?? null;
    }

    private function addWhereClause(): void
    {
        if (!empty($this->wheres)) {
            $this->sql .= ' WHERE ' . implode(' AND ', $this->wheres);
        }
    }

    private function execute(): bool
    {
        $stmt = $this->pdo->prepare($this->sql);
        return $stmt->execute(array_merge($this->params, $this->bindings));
    }

    private function fetchAll(): array
    {
        $stmt = $this->pdo->prepare($this->sql);
        $stmt->execute(array_merge($this->params, $this->bindings));
        return $stmt->fetchAll();
    }
}