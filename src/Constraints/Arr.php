<?php

namespace ZnCore\Domain\Constraints;

use Symfony\Component\Validator\Constraint;

class Arr extends Constraint
{

    public $message = 'The item id "{{ string }}" does not belong to the book "{{ bookName }}"';
    public $bookName;
}
