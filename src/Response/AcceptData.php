<?php

namespace App\Response;

use App\Interface\StructureResponse;

class AcceptData implements StructureResponse
{
    const ID = 'data';

    public function attach(StructureResponse $struct, ?string $id = null): self
    {
        if ($id === null) {
            $id = $struct::ID;
        }
        $this->{$id} = $struct;
        return $this;
    }
}
