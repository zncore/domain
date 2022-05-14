<?php

namespace ZnCore\Domain\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class Enum
 * @package ZnCore\Domain\Constraints
 * @see EnumValidator
 */
class Enum extends Constraint
{

    public $class;
    public $prefix;
    public $message = 'The value you selected is not a valid choice';
}
