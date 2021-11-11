<?php

namespace ZnCore\Domain\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class BooleanValidator extends BaseValidator
{

    protected $constraintClass = Boolean::class;

    public function validate($value, Constraint $constraint)
    {
        /*if (!$constraint instanceof Boolean) {
            throw new UnexpectedTypeException($constraint, Boolean::class);
        }

        // custom constraints should ignore null and empty values to allow
        // other constraints (NotBlank, NotNull, etc.) to take care of that
        if (null === $value || '' === $value) {
            return;
        }*/

        $this->checkConstraintType($constraint);
        if ($this->isEmptyStringOrNull($value)) {
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
