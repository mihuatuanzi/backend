<?php

namespace App\Response;

use App\Interface\ListResponse;
use App\Interface\StructureResponse;

class AcceptData implements StructureResponse
{
    const KEY_SINGULAR = 'data';
    const KEY_PLURAL = 'data';

    /**
     * @param StructureResponse|ListResponse $struct
     * @return $this
     */
    public function attach(StructureResponse|ListResponse $struct): self
    {
        if ($struct instanceof ListOf) {
            $key = $struct->getPrototype()->getConstant('KEY_PLURAL');
            $value = $struct->getList();
        } else {
            $key = $struct::KEY_SINGULAR;
            $value = $struct;
        }
        $this->{$key} = $value;
        return $this;
    }
}
