<?php

namespace ZnCore\Domain\Service\Interfaces;

use ZnCore\Base\Libs\Validation\Exceptions\UnprocessibleEntityException;
use ZnCore\Domain\Entity\Interfaces\EntityIdInterface;
use ZnCore\Base\Exceptions\NotFoundException;

interface ModifyInterface
{

    /**
     * @param array $data
     * @return EntityIdInterface
     * @throws UnprocessibleEntityException
     */
    public function create($data): EntityIdInterface;

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