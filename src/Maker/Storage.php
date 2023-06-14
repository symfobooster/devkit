<?php

namespace Symfobooster\Devkit\Maker;

class Storage
{
    private array $storage = [];

    public function set(string $key, $value): void
    {
        $this->storage[$key] = $value;
    }

    public function get(string $key): mixed
    {
        return $this->storage[$key] ?? null;
    }
}
