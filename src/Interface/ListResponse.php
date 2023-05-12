<?php

namespace App\Interface;

use ArrayAccess;
use ReflectionClass;

interface ListResponse
{
    public function getList(): ArrayAccess|array;

    public function getPrototype(): ReflectionClass;
}
