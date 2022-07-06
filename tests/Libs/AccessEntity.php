<?php

namespace ZnCore\Domain\Tests\Libs;

use Symfony\Component\Validator\Mapping\ClassMetadata;
use ZnCore\Entity\Interfaces\EntityIdInterface;
use ZnCore\Validation\Interfaces\ValidationByMetadataInterface;
use Symfony\Component\Validator\Constraints as Assert;

class AccessEntity implements EntityIdInterface, ValidationByMetadataInterface
{

    private $id = null;
    private $userId = null;
    private $projectId = null;

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('userId', new Assert\NotBlank);
        $metadata->addPropertyConstraint('userId', new Assert\Positive);
        $metadata->addPropertyConstraint('projectId', new Assert\NotBlank);
        $metadata->addPropertyConstraint('projectId', new Assert\Positive);
    }

    public function setId($value)
    {
        $this->id = $value;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setUserId($value)
    {
        $this->userId = $value;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function setProjectId($value)
    {
        $this->projectId = $value;
    }

    public function getProjectId()
    {
        return $this->projectId;
    }

}
