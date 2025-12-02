<?php

namespace SwiftPHP\Core;

class ModelQuery
{
    private Model $model;
    private array $relations;
    private array $wheres = [];

    public function __construct(Model $model, array $relations = [])
    {
        $this->model = $model;
        $this->relations = $relations;
    }

    public function where(string $column, string $operator, $value): self
    {
        $this->wheres[] = compact('column', 'operator', 'value');
        return $this;
    }

    public function get(): array
    {
        $modelClass = get_class($this->model);
        
        if (empty($this->wheres)) {
            $results = $modelClass::all();
        } else {
            $firstWhere = $this->wheres[0];
            $results = $modelClass::where($firstWhere['column'], $firstWhere['operator'], $firstWhere['value']);
        }
        
        if (!empty($this->relations)) {
            foreach ($results as $result) {
                foreach ($this->relations as $relation) {
                    if (method_exists($result, $relation)) {
                        $result->attributes[$relation] = $result->$relation();
                    }
                }
            }
        }
        
        return $results;
    }

    public function first(): ?Model
    {
        $results = $this->get();
        return $results[0] ?? null;
    }
}
