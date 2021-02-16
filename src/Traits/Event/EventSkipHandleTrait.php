<?php

namespace ZnCore\Domain\Traits\Event;

use ZnCore\Domain\Interfaces\Libs\EntityManagerInterface;

trait EventSkipHandleTrait
{

    private $skipHandle = false;

    public function isSkipHandle(): bool
    {
        return $this->skipHandle;
    }

    public function skipHandle(): void
    {
        $this->skipHandle = true;
    }
}
