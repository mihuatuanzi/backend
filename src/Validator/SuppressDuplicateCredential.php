<?php

namespace App\Validator;

use App\Config\AuthCredentialType;
use Attribute;
use Symfony\Component\Validator\Attribute\HasNamedArguments;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class SuppressDuplicateCredential extends Constraint
{
    public string $message = '不能重复使用';

    #[HasNamedArguments]
    public function __construct(
        public readonly ?AuthCredentialType $credentialType = null,
        public readonly ?string $credentialTypeExpr = null,
        public readonly bool $pass = false,
        mixed $options = null,
        array $groups = null,
        mixed $payload = null
    )
    {
        parent::__construct($options, $groups, $payload);
    }
}
