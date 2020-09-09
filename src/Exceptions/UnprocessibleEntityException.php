<?php

namespace ZnCore\Domain\Exceptions;

use Exception;
use Illuminate\Support\Collection;
use ZnCore\Domain\Entities\ValidateErrorEntity;

class UnprocessibleEntityException extends Exception
{

    private $errorCollection;

    public function setErrorCollection(Collection $errorCollection)
    {
        $this->errorCollection = $errorCollection;
    }

    /**
     * @return array | Collection | ValidateErrorEntity[]
     */
    public function getErrorCollection(): Collection
    {
        return $this->errorCollection;
    }

    public function add(string $field, string $message)
    {
        if(!isset($this->errorCollection)) {
            $this->errorCollection = new Collection;
        }
        $this->errorCollection[] = new ValidateErrorEntity($field, $message);
    }
}
