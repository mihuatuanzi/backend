<?php

namespace App\Response;

use App\Interface\StructureResponse;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class Violation implements StructureResponse
{
    const KEY_SINGULAR = 'violation';
    const KEY_PLURAL = 'violations';

    public array $_violations = [];

    public function withConstraints(ConstraintViolationListInterface $violationMap): self
    {
        foreach ($violationMap as $violation) {
            if ($propertyPath = $violation->getPropertyPath()) {
                $this->{$propertyPath} = $violation->getMessage();
            } else {
                $this->_violations[] = $violation->getMessage();
            }
        }
        return $this;
    }

    public function withMessages(string|array $message): self
    {
        foreach ((array)$message as $key => $item) {
            if (is_numeric($key)) {
                $this->_violations[] = $item;
            } else {
                $this->{$key} = $item;
            }
        }
        return $this;
    }
}
