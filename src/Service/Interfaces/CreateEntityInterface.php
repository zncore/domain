<?php

namespace ZnCore\Domain\Service\Interfaces;

interface CreateEntityInterface
{

    /**
     * Создать сущность
     *
     * Создавать новые сущности должен уметь только сервис
     *
     * @param array $attributes
     * @return object
     */
    public function createEntity(array $attributes = []);

}