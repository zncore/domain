<?php

namespace ZnCore\Domain\Domain\Traits;

use App\Bundles\Debug\Domain\Entities\ProfilingEntity;
use Illuminate\Support\Enumerable;
use ZnCore\Domain\Entity\Interfaces\EntityIdInterface;
use ZnCore\Domain\Entity\Interfaces\UniqueInterface;
use ZnCore\Domain\Query\Entities\Query;

trait FindOneTrait
{

//    abstract public function oneById($id, Query $query = null): EntityIdInterface;

//    abstract public function oneByUnique(UniqueInterface $entity): EntityIdInterface;

    public function findOneById($id, Query $query = null): EntityIdInterface {
        return $this->oneById($id, $query);
    }

    /*public function findOneByUnique(UniqueInterface $entity): EntityIdInterface {
        return $this->oneByUnique($entity);
    }*/
}
