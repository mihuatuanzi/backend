<?php

namespace App\Validator;

use App\Repository\AuthenticationRepository;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class SuppressDuplicateCredentialValidator extends ConstraintValidator
{
    public function __construct(
        private readonly AuthenticationRepository $authenticationRepository,
        private readonly ExpressionLanguage       $expressionLanguage
    ) {}

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof SuppressDuplicateCredential) {
            throw new UnexpectedTypeException($constraint, SuppressDuplicateCredential::class);
        }

        if ((!$constraint->credentialType && !$constraint->credentialTypeExpr) || !$value) {
            return;
        }

        $credentialType = $constraint->credentialType;
        if (!$credentialType) {
            $context = $this->context;
            $variables = [];
            $variables['value'] = $value;
            $variables['this'] = $context->getObject();
            $credentialType = $this->expressionLanguage->evaluate($constraint->credentialTypeExpr, $variables);
        }

        $count = $this->authenticationRepository->count([
            'credential_type' => $credentialType,
            'credential_key' => $value
        ]);

        if ($count > 0) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
