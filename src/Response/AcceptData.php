<?php

namespace App\Response;

use App\Interface\StructureResponse;

class AcceptData implements StructureResponse
{
    const KEY_SINGULAR = 'data';
    const KEY_PLURAL = 'data';

    /**
     * @param StructureResponse|StructureResponse[] $struct
     * @return $this
     */
    public function attach(StructureResponse|array $struct): self
    {
        $key = is_array($struct) ? $struct::KEY_PLURAL : $struct::KEY_SINGULAR;
        $this->{$key} = $struct;
        return $this;
    }
}
