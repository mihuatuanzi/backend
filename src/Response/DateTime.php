<?php

namespace App\Response;

use App\Interface\StructureResponse;

class DateTime implements StructureResponse
{
    const ID = 'datetime';

    public string $datetime;

    public function __construct(
        public string $time_zone = 'UTC'
    )
    {
    }

    public function withDateTime(\DateTime $dateTime): self
    {
        $this->datetime = $dateTime->format('Y-m-d H:i:s');
        return $this;
    }
}