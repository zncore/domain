<?php

namespace ZnCore\Domain\Relation\Libs;

use Illuminate\Support\Collection;
use ZnBundle\Eav\Domain\Repositories\Eloquent\FieldRepository;
use ZnCore\Domain\Helpers\Repository\RelationHelper;
use ZnCore\Domain\Domain\Interfaces\ReadAllInterface;
use ZnCore\Domain\Query\Entities\Query;

class QueryFilter
{

    private $repository;
    private $query;

    public function __construct(ReadAllInterface $repository, Query $query)
    {
        $this->repository = $repository;
        $this->query = $query;
    }

    public function loadRelations(Collection $collection)
    {
        if (method_exists($this->repository, 'relations')) {
            $relationLoader = new RelationLoader;
            $relationLoader->setRelations($this->repository->relations());
            $relationLoader->setRepository($this->repository);
            $relationLoader->loadRelations($collection, $this->query);
        }
    }
}
