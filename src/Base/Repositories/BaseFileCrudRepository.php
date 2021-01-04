<?php

namespace ZnCore\Domain\Base\Repositories;

use ZnCore\Base\Libs\Store\StoreFile;

abstract class BaseFileCrudRepository extends BaseArrayCrudRepository
{

    abstract public function fileName(): string;

    protected function getItems(): array
    {
        $store = new StoreFile($this->fileName());
        return $store->load();
    }
}
