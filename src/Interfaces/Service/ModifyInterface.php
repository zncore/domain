<?php

namespace ZnCore\Domain\Interfaces\Service;

use ZnCore\Domain\Exceptions\UnprocessibleEntityException;
use ZnCore\Domain\Interfaces\Entity\EntityIdInterface;
use ZnCore\Base\Exceptions\NotFoundException;

interface ModifyInterface
{

    /**
     * @param array $attributes
     * @return EntityIdInterface
     * @throws UnprocessibleEntityException
     */
    public function create($attributes): EntityIdInterface;

    /**
     * @param int $id
     * @param array $data
     * @throws NotFoundException
     * @throws UnprocessibleEntityException
     */
    public function updateById($id, $data);

    /**
     * @param int $id
     * @throws NotFoundException
     */
    public function deleteById($id);

}