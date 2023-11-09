<?php declare(strict_types=1);

namespace App\Global\Business;

class Container
{
    private array $object = [];

    public function set(string $class, object $object): void
    {
        $this->object[$class] = $object;
    }

    public function get(string $class): object
    {
        return $this->object[$class];
    }

    public function getList(): array
    {
        return $this->object;
    }
}