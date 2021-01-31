<?php

namespace ZnCore\Domain\Interfaces\Entity;

interface ValidateEntityInterface
{

    /**
     * @deprecated 
     * @see ValidateEntityByMetadataInterface 
     */
    public function validationRules();
}