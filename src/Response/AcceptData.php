<?php

namespace App\Response;

use App\Interface\StructureResponse;

class AcceptData implements StructureResponse
{
    const SINGULAR = 'data';
    const PLURAL = 'data';

    /**
     * @param StructureResponse|StructureResponse[] $struct
     * @return $this
     */
    public function attach(StructureResponse|array $struct): self
    {
        $key = is_array($struct) ? $struct::PLURAL : $struct::SINGULAR;
        $this->{$key} = $struct;
        return $this;
    }
}
