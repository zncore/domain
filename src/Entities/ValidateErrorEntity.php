<?php

namespace ZnCore\Domain\Entities;

use Symfony\Component\Validator\ConstraintViolationInterface;

class ValidateErrorEntity
{

    private $field;
    private $message;
    private $violation;

    public function __construct(string $field = null, string $message = null)
    {
        $this->field = $field;
        $this->message = $message;
    }

    public function getField()
    {
        return $this->field;
    }

    public function setField($field): void
    {
        $this->field = $field;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function setMessage($message): void
    {
        $this->message = $message;
    }

    public function setViolation(ConstraintViolationInterface $violation)
    {
        $this->violation = $violation;
    }

    public function getViolation()
    {
        return $this->violation;
    }
}
