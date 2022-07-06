<?php

namespace ZnCore\Domain\Repository\Interfaces;

use ZnCore\Validation\Exceptions\UnprocessibleEntityException;
use ZnCore\Entity\Interfaces\EntityIdInterface;
use ZnCore\Entity\Exceptions\NotFoundException;

interface ModifyInterface
{
    /**
     * @param EntityIdInterface | object $entity
     * @throws UnprocessibleEntityException
     */
    public function create(EntityIdInterface $entity);

    /*
     * @param EntityIdInterface | object $entity
     * @throws UnprocessibleEntityException
     */
    //public function persist(EntityIdInterface $entity);

    /**
     * @param EntityIdInterface | object $entity
     * @throws NotFoundException
     * @throws UnprocessibleEntityException
     */
    public function update(EntityIdInterface $entity);

    /*
     * @param int $id
     * @param array $data
     * @throws NotFoundException
     * @throws UnprocessibleEntityException
     */
    //public function updateById($id, $data);

    /**
     * @param int $id
     * @throws NotFoundException
     */
    public function deleteById($id);

    public function deleteByCondition(array $condition);

}