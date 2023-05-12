<?php

namespace App\Response;

use App\Interface\StructureResponse;

class Message implements StructureResponse
{
    const KEY_SINGULAR = 'message';
    const KEY_PLURAL = 'messages';

    public string $content = '';

    public null|array|object $annotation = null;

    public int $create_at;

    public function with(string $content): self
    {
        $this->content = $content;
        $this->create_at = time();
        return $this;
    }

    public function withAnnotation(array|object $annotation): self
    {
        $this->annotation = $annotation;
        return $this;
    }
}
