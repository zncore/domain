<?php

namespace ZnCore\Domain\Constraints;

use Symfony\Component\Validator\Constraint;

class Enum extends Constraint
{

    public $class;
    public $prefix;
    public $message = 'The value you selected is not a valid choice';
}
