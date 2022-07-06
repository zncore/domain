<?php

namespace ZnCore\Domain\Service\Interfaces;

use ZnCore\Validation\Exceptions\UnprocessibleEntityException;
use ZnCore\Entity\Interfaces\EntityIdInterface;
use ZnCore\Entity\Exceptions\NotFoundException;

interface ModifyInterface
{

    /**
     * Создать и сохранить сущность в хранилище
     * @param array $data массив атрибутов сущности
     * @return EntityIdInterface
     * @throws UnprocessibleEntityException
     */
    public function create($data): EntityIdInterface;

    /**
     * Редактировать запись в хранилище по ID
     * @param int $id ID сущности
     * @param array $data массив атрибутов сущности
     * @throws NotFoundException
     * @throws UnprocessibleEntityException
     */
    public function updateById($id, $data);

    /**
     * Удалить запись из хранилища по ID
     * @param int $id ID сущности
     * @throws NotFoundException
     */
    public function deleteById($id);

}