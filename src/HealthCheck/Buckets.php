<?php

namespace WecarSwoole\HealthCheck;

class Buckets
{
    private $size;
    private $current;
    private $container;

    public function __construct(int $size)
    {
        $this->size = $size;
        $this->current = 0;
        $this->container = array();
    }

    public function push($item)
    {
        if ($this->current == $this->size) {
            array_shift($this->container);
        } else {
            $this->current++;
        }

        $this->container[] = $item;
    }

    public function get(int $index)
    {
        return $this->container[$index];
    }

    public function current(): int
    {
        return $this->current;
    }

    public function size(): int
    {
        return $this->size;
    }

    public function toArray(): array
    {
        return $this->container;
    }

    public function __toString(): string
    {
        return print_r($this->container, true);
    }
}
