<?php

namespace App\Response;

use App\Interface\ListResponse;
use ArrayAccess;
use ReflectionClass;
use ReflectionException;

class ListOf implements ListResponse
{
    private ArrayAccess|array $list = [];

    private ReflectionClass $prototype;

    /**
     * @throws ReflectionException
     */
    public function with(ArrayAccess|array $list, object|string $objectOrClass): self
    {
        $this->list = $list;
        $this->prototype = new ReflectionClass($objectOrClass);
        return $this;
    }

    public function getList(): ArrayAccess|array
    {
        return $this->list;
    }

    public function getPrototype(): ReflectionClass
    {
        return $this->prototype;
    }
}
