<?php

namespace ZnCore\Domain\Tests\Libs;

use ZnCore\Domain\Interfaces\Entity\EntityIdInterface;
use ZnCore\Domain\Interfaces\Entity\ValidateEntityInterface;
use Symfony\Component\Validator\Constraints as Assert;

class AccessEntity implements EntityIdInterface, ValidateEntityInterface
{

    private $id = null;
    private $userId = null;
    private $projectId = null;

    public function validationRules(): array
    {
        return [
            'userId' => [
                new Assert\NotBlank,
                new Assert\Positive,

            ],
            'projectId' => [
                new Assert\NotBlank,
                new Assert\Positive,
            ],
        ];
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
