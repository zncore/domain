<?php

namespace ZnCore\Domain\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

abstract class BaseValidator extends ConstraintValidator
{

    protected $constraintClass = PersonName::class;

    public function constraintClass(): string
    {
        return $this->constraintClass;
    }

    protected function checkConstraintType(Constraint $constraint) {
        $constraintClass = $this->constraintClass();
        if (!$constraint instanceof $constraintClass) {
            throw new UnexpectedTypeException($constraint, $constraintClass);
        }
    }

    protected function isEmpty($value): bool {
        // custom constraints should ignore null and empty values to allow
        // other constraints (NotBlank, NotNull, etc.) to take care of that
        return null === $value || '' === $value;
    }

    public function validate($value, Constraint $constraint)
    {
        $this->checkConstraintType($constraint);
        if ($this->isEmpty($value)) {
            return;
        }

        if (!is_bool($value)) {
            // throw this exception if your validator cannot handle the passed type so that it can be marked as invalid
            throw new UnexpectedValueException($value, 'boolean');

            // separate multiple types using pipes
            // throw new UnexpectedValueException($value, 'string|int');
        }
    }
}
