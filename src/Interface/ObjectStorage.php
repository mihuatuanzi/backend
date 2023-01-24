<?php

namespace App\Interface;

interface ObjectStorage
{
    public function get(string $object): string;
    public function put(string $object, string $file): void;
}
