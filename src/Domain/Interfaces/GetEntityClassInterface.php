<?php

namespace ZnCore\Domain\Domain\Interfaces;

interface GetEntityClassInterface
{

    /**
     * Получить имя класса сущности, с которой работает сервис
     * @return string
     */
    public function getEntityClass(): string;

}