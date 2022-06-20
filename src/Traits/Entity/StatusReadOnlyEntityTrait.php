<?php

namespace ZnCore\Domain\Traits\Entity;

use ZnCore\Base\Exceptions\ReadOnlyException;
use ZnCore\Base\Helpers\DeprecateHelper;
use ZnCore\Base\Helpers\Helper;

//DeprecateHelper::softThrow();

/**
 * Trait StatusReadOnlyEntityTrait
 * @package ZnCore\Domain\Traits\Entity
 * @deprecated
 * @see Helper::checkReadOnly()
 */
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
