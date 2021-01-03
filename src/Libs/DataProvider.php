<?php

namespace ZnCore\Domain\Libs;

use Illuminate\Support\Collection;
use ZnCore\Domain\Entities\DataProviderEntity;
use ZnCore\Domain\Interfaces\Entity\ValidateEntityInterface;
use ZnCore\Domain\Interfaces\ForgeQueryByFilterInterface;
use ZnCore\Domain\Interfaces\ReadAllInterface;

class DataProvider
{

    /** @var ReadAllInterface */
    private $service;

    /** @var Query */
    private $query;

    /** @var DataProviderEntity */
    private $entity;

    /** @var ValidateEntityInterface */
    private $filterModel;

    public function __construct(object $service, Query $query = null, int $page = 1, int $pageSize = 10)
    {
        $this->service = $service;
        $this->query = Query::forge($query);
        $this->entity = new DataProviderEntity;
        $this->entity->setPage($query->getParam(Query::PAGE) ?: $page);
//        $this->entity->setPage($page);
        $this->entity->setPageSize($query->getParam(Query::PER_PAGE) ?: $pageSize);
//        $this->entity->setPageSize($pageSize);
    }

    public function setService(object $service)
    {
        $this->service = $service;
    }

    public function getService(): ?object
    {
        return $this->service;
    }

    public function setQuery(Query $query)
    {
        $this->query = $query;
    }

    public function getQuery(): ?Query
    {
        return $this->query;
    }

    public function setEntity(DataProviderEntity $entity)
    {
        $this->entity = $entity;
    }

    public function getEntity(): ?DataProviderEntity
    {
        return $this->entity;
    }

    public function getFilterModel(): ValidateEntityInterface
    {
        return $this->filterModel;
    }

    public function setFilterModel(ValidateEntityInterface $filterModel): void
    {
        $this->filterModel = $filterModel;
    }

    public function getAll(): DataProviderEntity
    {
        $this->entity->setTotalCount($this->getTotalCount());
        $this->entity->setCollection($this->getCollection());
        return $this->entity;
    }

    private function forgeQueryByFilter()
    {

    }

    private function forgeQuery(): Query
    {
        $query = clone $this->query;
        if ($this->filterModel) {
            if ($this->service instanceof ForgeQueryByFilterInterface) {
                $this->service->forgeQueryByFilter($this->filterModel, $query);
            }
        }
        return $query;
    }

    public function getCollection(): Collection
    {
        if ($this->entity->getCollection() === null) {
            $query = $this->forgeQuery();
            $query->limit($this->entity->getPageSize());
            $query->offset($this->entity->getPageSize() * ($this->entity->getPage() - 1));
            $this->entity->setCollection($this->service->all($query));
        }
        return $this->entity->getCollection();
    }

    public function getTotalCount(): int
    {
        if ($this->entity->getTotalCount() === null) {
            $query = $this->forgeQuery();
            $query->removeParam(Query::PER_PAGE);
            $query->removeParam(Query::LIMIT);
            $query->removeParam(Query::ORDER);
            $this->entity->setTotalCount($this->service->count($query));
        }
        return $this->entity->getTotalCount();
    }
}
