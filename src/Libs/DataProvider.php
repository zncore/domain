<?php

namespace ZnCore\Domain\Libs;

use Illuminate\Support\Collection;
use ZnCore\Base\Helpers\ClassHelper;
use ZnCore\Contract\Domain\Interfaces\DataProviderInterface;
use ZnCore\Contract\Domain\Interfaces\ForgeQueryByFilterInterface;
use ZnCore\Domain\Entities\DataProviderEntity;
use ZnCore\Domain\Interfaces\ReadAllInterface;

class DataProvider implements DataProviderInterface
{

    /** @var ReadAllInterface */
    protected $service;

    /** @var Query */
    protected $query;

    /** @var DataProviderEntity */
    protected $entity;

    protected $filterModel;

    protected $page;

    protected $pageSize;

    public function __construct(object $service, Query $query = null, int $page = 1, int $pageSize = 10)
    {
        $this->service = $service;
        $this->query = Query::forge($query);
        $this->entity = new DataProviderEntity;
        $this->entity->setPage($this->query->getParam(Query::PAGE) ?: $page);
//        $this->entity->setPage($page);
        $this->entity->setPageSize($this->query->getParam(Query::PER_PAGE) ?: $pageSize);
//        $this->entity->setPageSize($pageSize);
    }

    public function getPage(): int
    {
        return $this->entity->getPage();
//        return $this->page;
    }

    public function setPage(int $page): void
    {
        $this->entity->setPage($page);
//        $this->page = $page;
    }

    public function getPageSize(): int
    {
        return $this->entity->getPageSize();
//        return $this->pageSize;
    }

    public function setPageSize(int $pageSize): void
    {
        $this->entity->setPageSize($pageSize);
//        $this->pageSize = $pageSize;
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

    public function getFilterModel(): ?object
    {
        if ($this->getQuery() && $this->getQuery()->getFilterModel()) {
            return $this->getQuery()->getFilterModel();
        }
        return $this->filterModel;
    }

    public function setFilterModel(object $filterModel): void
    {
        //$this->getQuery()->setFilterModel($filterModel);
        $this->filterModel = $filterModel;
    }

    public function getAll(): DataProviderEntity
    {
        $this->entity->setTotalCount($this->getTotalCount());
        $this->entity->setCollection($this->getCollection());
        return $this->entity;
    }

    protected function forgeQuery(): Query
    {
        $filterModel = $this->filterModel;
        $query = clone $this->query;
        if ($this->filterModel) {
            if ($filterModel instanceof ForgeQueryByFilterInterface) {
                $filterModel->forgeQueryByFilter($filterModel, $query);
            } else {
                ClassHelper::checkInstanceOf($this->service, ForgeQueryByFilterInterface::class);
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
