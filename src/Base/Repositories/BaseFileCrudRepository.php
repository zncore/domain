<?php

namespace ZnCore\Domain\Base\Repositories;

use ZnCore\Base\Exceptions\NotImplementedMethodException;
use ZnCore\Base\Legacy\Yii\Helpers\FileHelper;
use ZnCore\Base\Libs\DotEnv\DotEnv;
use ZnCore\Base\Libs\Store\StoreFile;

abstract class BaseFileCrudRepository extends BaseArrayCrudRepository
{

    public function tableName(): string
    {
        throw new NotImplementedMethodException('Not Implemented Method "tableName"');
    }

    public function fileName(): string
    {
        $tableName = $this->tableName();
        $root = FileHelper::rootPath();
        $directory = DotEnv::get('FILE_DB_DIRECTORY');
        return "$root/$directory/$tableName.php";
    }

    protected function getItems(): array
    {
        $store = new StoreFile($this->fileName());
        return $store->load() ?: [];
    }

    protected function setItems(array $items)
    {
        $store = new StoreFile($this->fileName());
        return $store->save($items);
    }
}
