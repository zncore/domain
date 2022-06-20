<?php

namespace ZnCore\Domain\Entities;

use ZnCore\Base\Helpers\DeprecateHelper;

DeprecateHelper::hardThrow();

class EventEntity
{

    private $isAllow = false;
    private $type;
    private $target;
    private $method;
    private $data;

    public function getIsAllow(): bool
    {
        return $this->isAllow;
    }

    public function setIsAllow(bool $isAllow): void
    {
        $this->isAllow = $isAllow;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getTarget(): object
    {
        return $this->target;
    }

    public function setTarget(object $target): void
    {
        $this->target = $target;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function setMethod(string $method): void
    {
        $this->method = $method;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setData($data): void
    {
        $this->data = $data;
    }
}
