<?php

namespace ZnCore\Domain\Traits\Entity;

use ZnCore\Base\Exceptions\ReadOnlyException;

trait StatusReadOnlyEntityTrait
{

    private $_statusId = null;

    public function setStatusId(int $value): void
    {
        if ($this->_statusId != null) {
            throw new ReadOnlyException('The "statusId" attribute is read-only');
        }
        $this->statusId = $value;
        $this->_statusId = $value;
    }

    public function getStatusId(): int
    {
        return $this->statusId;
    }
}
