<?php

declare(strict_types=1);

namespace SwiftPHP\Core;

class Container
{
    private array $bindings = [];
    private array $instances = [];

    public function bind(string $abstract, $concrete = null): void
    {
        $this->bindings[$abstract] = $concrete ?? $abstract;
    }

    public function singleton(string $abstract, $concrete = null): void
    {
        $this->bind($abstract, $concrete);
    }

    public function resolve(string $abstract)
    {
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        $concrete = $this->bindings[$abstract] ?? $abstract;

        if ($concrete instanceof \Closure) {
            $instance = $concrete($this);
        } else {
            $instance = $this->build($concrete);
        }

        $this->instances[$abstract] = $instance;
        return $instance;
    }

    private function build(string $concrete)
    {
        $reflection = new \ReflectionClass($concrete);

        if (!$reflection->isInstantiable()) {
            throw new \Exception("Class {$concrete} is not instantiable");
        }

        $constructor = $reflection->getConstructor();

        if (!$constructor) {
            return new $concrete();
        }

        $dependencies = [];
        foreach ($constructor->getParameters() as $parameter) {
            // Check for Inject attribute (PHP 8.1+)
            $attributes = $parameter->getAttributes(\SwiftPHP\Attributes\Inject::class);
            if (!empty($attributes)) {
                $attribute = $attributes[0]->newInstance();
                $dependencies[] = $this->resolve($attribute->class);
                continue;
            }

            $type = $parameter->getType();
            if ($type && !$type->isBuiltin()) {
                $dependencies[] = $this->resolve($type->getName());
            } elseif ($parameter->isDefaultValueAvailable()) {
                $dependencies[] = $parameter->getDefaultValue();
            }
        }

        return $reflection->newInstanceArgs($dependencies);
    }
}
