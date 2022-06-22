<?php

namespace ZnCore\Domain\Domain\Events;

use Symfony\Contracts\EventDispatcher\Event;
use ZnCore\Domain\Query\Entities\Query;

class QueryEvent extends Event
{

    private $query;
    private $filterModel;

    public function __construct(Query $query)
    {
        $this->query = $query;
    }

    public function getQuery(): Query
    {
        return $this->query;
    }

    public function getFilterModel(): ?object
    {
        return $this->filterModel;
    }

    public function setFilterModel(object $filterModel): void
    {
        $this->filterModel = $filterModel;
    }
}
