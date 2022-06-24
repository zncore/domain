<?php

namespace ZnCore\Domain\Service\Traits;

use ZnCore\Contract\Common\Exceptions\InvalidMethodParameterException;
use ZnCore\Domain\Domain\Enums\EventEnum;
use ZnCore\Domain\Entity\Interfaces\EntityIdInterface;
use ZnCore\Domain\Entity\Interfaces\UniqueInterface;
use ZnCore\Domain\Query\Entities\Query;

trait CrudServiceFindOneTrait
{

    public function oneById($id, Query $query = null): EntityIdInterface
    {
        if (empty($id)) {
            throw (new InvalidMethodParameterException('Empty ID'))
                ->setParameterName('id');
        }
        $query = $this->forgeQuery($query);
        $entity = $this->getRepository()->oneById($id, $query);
        $event = $this->dispatchEntityEvent($entity, EventEnum::AFTER_READ_ENTITY);
        return $entity;
    }

    public function oneByUnique(UniqueInterface $entity): EntityIdInterface
    {
        return $this->getRepository()->oneByUnique($entity);
    }
}
