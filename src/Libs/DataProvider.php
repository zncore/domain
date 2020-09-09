<?php

namespace ZnCore\Domain\Libs;

use Illuminate\Support\Collection;
use ZnCore\Domain\Entities\DataProviderEntity;
use ZnCore\Base\Legacy\Yii\Helpers\ArrayHelper;
use ZnCore\Domain\Interfaces\ReadAllInterface;

class DataProvider
{

    /** @var ReadAllInterface */
    private $service;

    /** @var Query */
    private $query;

    /** @var DataProviderEntity */
    private $entity;

    public function __construct(object $service, Query $query = null, int $page = 1, int $pageSize = 10)
    {
        $this->service = $service;
        $this->query = Query::forge($query);
        $this->entity = new DataProviderEntity;
        $this->entity->setPage($page);
        $this->entity->setPageSize($pageSize);
    }

    public function setService(object $service) {
        $this->service = $service;
    }

    public function getService(): ?object {
        return $this->service;
    }

    public function setQuery(Query $query) {
        $this->query = $query;
    }

    public function getQuery(): ?Query {
        return $this->query;
    }

    public function setEntity(DataProviderEntity $entity) {
        $this->entity = $entity;
    }

    public function getEntity(): ?DataProviderEntity {
        return $this->entity;
    }

    public function getAll(): DataProviderEntity
    {
        $this->entity->setTotalCount($this->getTotalCount());
        $this->entity->setCollection($this->getCollection());
        return $this->entity;
    }

    private function getCollection(): Collection
    {
        if ($this->entity->getCollection() === null) {
            $query = clone $this->query;
            $query->limit($this->entity->getPageSize());
            $query->offset($this->entity->getPageSize() * ($this->entity->getPage() - 1));
            $this->entity->setCollection($this->service->all($query));
        }
        return $this->entity->getCollection();
    }

    private function getTotalCount(): int
    {
        if ( $this->entity->getTotalCount() === null) {
            $query = clone $this->query;
            $query->removeParam(Query::PER_PAGE);
            $query->removeParam(Query::LIMIT);
            $query->removeParam(Query::ORDER);
            $this->entity->setTotalCount($this->service->count($query));
        }
        return $this->entity->getTotalCount();
    }

}