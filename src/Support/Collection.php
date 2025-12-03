<?php

declare(strict_types=1);

namespace SwiftPHP\Support;

class Collection implements \ArrayAccess, \Iterator, \Countable, \JsonSerializable
{
    private array $items;
    private int $position = 0;

    public function __construct(array $items = [])
    {
        $this->items = array_values($items);
    }

    public static function make(array $items = []): self
    {
        return new self($items);
    }

    public function all(): array
    {
        return $this->items;
    }

    public function map(callable $callback): self
    {
        return new self(array_map($callback, $this->items));
    }

    public function filter(callable $callback = null): self
    {
        if ($callback === null) {
            return new self(array_filter($this->items));
        }
        return new self(array_filter($this->items, $callback, ARRAY_FILTER_USE_BOTH));
    }

    public function reduce(callable $callback, $initial = null)
    {
        return array_reduce($this->items, $callback, $initial);
    }

    public function each(callable $callback): self
    {
        foreach ($this->items as $key => $item) {
            if ($callback($item, $key) === false) {
                break;
            }
        }
        return $this;
    }

    public function first(callable $callback = null)
    {
        if ($callback === null) {
            return $this->items[0] ?? null;
        }

        foreach ($this->items as $key => $item) {
            if ($callback($item, $key)) {
                return $item;
            }
        }

        return null;
    }

    public function last(callable $callback = null)
    {
        if ($callback === null) {
            return end($this->items) ?: null;
        }

        return $this->filter($callback)->last();
    }

    public function pluck(string $key): self
    {
        return $this->map(function ($item) use ($key) {
            return is_array($item) ? ($item[$key] ?? null) : ($item->$key ?? null);
        });
    }

    public function where(string $key, $value): self
    {
        return $this->filter(function ($item) use ($key, $value) {
            $itemValue = is_array($item) ? ($item[$key] ?? null) : ($item->$key ?? null);
            return $itemValue === $value;
        });
    }

    public function unique(): self
    {
        return new self(array_unique($this->items, SORT_REGULAR));
    }

    public function sort(callable $callback = null): self
    {
        $items = $this->items;

        if ($callback) {
            usort($items, $callback);
        } else {
            sort($items);
        }

        return new self($items);
    }

    public function sortBy(string $key, bool $descending = false): self
    {
        return $this->sort(function ($a, $b) use ($key, $descending) {
            $aVal = is_array($a) ? ($a[$key] ?? null) : ($a->$key ?? null);
            $bVal = is_array($b) ? ($b[$key] ?? null) : ($b->$key ?? null);

            $result = $aVal <=> $bVal;
            return $descending ? -$result : $result;
        });
    }

    public function groupBy(string $key): array
    {
        $grouped = [];

        foreach ($this->items as $item) {
            $groupKey = is_array($item) ? ($item[$key] ?? null) : ($item->$key ?? null);
            $grouped[$groupKey][] = $item;
        }

        return array_map(fn ($items) => new self($items), $grouped);
    }

    public function chunk(int $size): self
    {
        $chunks = array_chunk($this->items, $size);
        return new self(array_map(fn ($chunk) => new self($chunk), $chunks));
    }

    public function take(int $limit): self
    {
        return new self(array_slice($this->items, 0, $limit));
    }

    public function skip(int $count): self
    {
        return new self(array_slice($this->items, $count));
    }

    public function isEmpty(): bool
    {
        return empty($this->items);
    }

    public function isNotEmpty(): bool
    {
        return !$this->isEmpty();
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function sum(string $key = null)
    {
        if ($key === null) {
            return array_sum($this->items);
        }

        return $this->pluck($key)->sum();
    }

    public function avg(string $key = null)
    {
        $count = $this->count();
        return $count > 0 ? $this->sum($key) / $count : 0;
    }

    public function toArray(): array
    {
        return array_map(function ($item) {
            if ($item instanceof self) {
                return $item->toArray();
            }
            if (is_object($item) && method_exists($item, 'toArray')) {
                return $item->toArray();
            }
            return $item;
        }, $this->items);
    }

    public function toJson(int $options = 0): string
    {
        return json_encode($this->toArray(), $options);
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    // ArrayAccess implementation
    public function offsetExists($offset): bool
    {
        return isset($this->items[$offset]);
    }

    public function offsetGet($offset): mixed
    {
        return $this->items[$offset] ?? null;
    }

    public function offsetSet($offset, $value): void
    {
        if ($offset === null) {
            $this->items[] = $value;
        } else {
            $this->items[$offset] = $value;
        }
    }

    public function offsetUnset($offset): void
    {
        unset($this->items[$offset]);
    }

    // Iterator implementation
    public function current(): mixed
    {
        return $this->items[$this->position];
    }

    public function key(): int
    {
        return $this->position;
    }

    public function next(): void
    {
        ++$this->position;
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function valid(): bool
    {
        return isset($this->items[$this->position]);
    }
}
