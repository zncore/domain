<?php

namespace ZnCore\Domain\Interfaces\Entity;

interface ValidateEntityInterface
{

    /**
     * @return array
     */
    public function validationRules();

}