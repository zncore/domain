<?php

namespace ZnCore\Domain\DataProvider\Entities;

use ZnCore\Collection\Interfaces\Enumerable;

class DataProviderEntity
{

    private $page = 1;
    private $pageSize = 20;
    private $maxPageSize = 50;
    private $totalCount;
    private $pageCount;
    private $collection;

    public function setPage(int $page)
    {
        $this->page = $page < 1 ? 1 : $page;
    }

    public function getPage(): int
    {
        $pageCount = $this->getPageCount();
        if ($pageCount !== null && $this->page > $pageCount) {
            $this->page = $pageCount;
        }
        if ($this->page < 1) {
            $this->page = 1;
        }
        return $this->page;
    }

    public function setPageSize(int $pageSize)
    {
        $this->pageSize = $pageSize < 1 ? 1 : $pageSize;
    }

    public function getPageSize(): int
    {
        if ($this->pageSize > $this->maxPageSize) {
            $this->pageSize = $this->maxPageSize;
        }
        return $this->pageSize;
    }

    public function getMaxPageSize()
    {
        return $this->maxPageSize;
    }

    public function setMaxPageSize(int $maxPageSize): void
    {
        $this->maxPageSize = $maxPageSize;
    }

    public function getTotalCount()
    {
        return $this->totalCount;
    }

    public function setTotalCount(int $totalCount): void
    {
        $this->totalCount = $totalCount;
    }

    public function getPageCount(): ?int
    {
        if (!isset($this->totalCount)) {
            return null;
        }
        $totalCount = $this->totalCount;

        $this->pageCount = intval(ceil($totalCount / $this->getPageSize()));

        if ($this->pageCount < 1) {
            $this->pageCount = 1;
        }

        return $this->pageCount;
    }

    public function getCollection()
    {
        return $this->collection;
    }

    public function setCollection(Enumerable $collection): void
    {
        $this->collection = $collection;
    }

    public function isFirstPage(): bool
    {
        return $this->page == 1;
    }

    public function isLastPage(): bool
    {
        return $this->page == $this->pageCount;
    }

    public function getPrevPage(): int
    {
        return $this->page - 1;
    }

    public function getNextPage(): int
    {
        return $this->page + 1;
    }

}