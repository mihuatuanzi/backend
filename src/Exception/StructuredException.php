<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class StructuredException extends HttpException
{
    private array $data;

    public function __construct(array $data, int $code = 0, ?Throwable $previous = null)
    {
        $this->data = $data;
        parent::__construct(json_encode($data), $code, $previous);
    }

    public function getData(): array
    {
        return $this->data;
    }
}
