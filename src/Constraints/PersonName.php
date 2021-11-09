<?php

namespace ZnCore\Domain\Constraints;

use Symfony\Component\Validator\Constraint;

class PersonName extends Constraint
{

    public $message = 'The name "{{ value }}" must contain only letters';
}
