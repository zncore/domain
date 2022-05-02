<?php

namespace ZnCore\Domain\Traits\Entity;

use ZnCore\Contract\User\Exceptions\UnauthorizedException;
use ZnCore\Base\Enums\StatusEnum;

trait SoftDeleteEntityTrait
{

    abstract public function setStatusId(int $statusId): void;

    public function delete(): void
    {
        if($this->getStatusId() == StatusEnum::DELETED) {
            throw new \DomainException('The entry has already been deleted');
        }
        $this->statusId = StatusEnum::DELETED;
    }
}
