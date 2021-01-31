<?php

namespace ZnCore\Domain\Interfaces\Entity;

use Symfony\Component\Validator\Mapping\ClassMetadata;

interface ValidateEntityByMetadataInterface
{

    public static function loadValidatorMetadata(ClassMetadata $metadata);
}