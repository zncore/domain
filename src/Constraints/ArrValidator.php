<?php

namespace ZnCore\Domain\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class ArrValidator extends BaseValidator
{

    protected $constraintClass = Arr::class;

    public function validate($value, Constraint $constraint)
    {
        if ($value && !is_array($value)) {
            // throw this exception if your validator cannot handle the passed type so that it can be marked as invalid
            throw new UnexpectedValueException($value, 'array');

            // separate multiple types using pipes
            // throw new UnexpectedValueException($value, 'string|int');
        }
    }
}
