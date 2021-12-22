<?php

namespace ZnCore\Domain\Base\Repositories;

use ZnCore\Base\Exceptions\NotImplementedMethodException;
use ZnCore\Base\Legacy\Yii\Helpers\FileHelper;
use ZnCore\Base\Libs\DotEnv\DotEnv;
use ZnCore\Base\Libs\Store\StoreFile;
use ZnCore\Domain\Interfaces\Repository\CrudRepositoryInterface;
use ZnCore\Domain\Libs\Query;
use ZnCore\Domain\Traits\Repository\ArrayCrudRepositoryTrait;

abstract class BaseFileCrudRepository extends BaseFileRepository implements CrudRepositoryInterface
{

    use ArrayCrudRepositoryTrait;

    public function tableName(): string
    {
        throw new NotImplementedMethodException('Not Implemented Method "tableName"');
    }

    public function directory(): string
    {
        return DotEnv::get('FILE_DB_DIRECTORY');
    }

    public function fileExt(): string
    {
        return 'php';
    }

    /*public function _relations()
    {
        return [];
    }*/

    protected function forgeQuery(Query $query = null)
    {
        $query = Query::forge($query);
        return $query;
    }

    public function fileName(): string
    {
        $tableName = $this->tableName();
        $root = FileHelper::rootPath();
        $directory = $this->directory();
        $ext = $this->fileExt();
        $path = "$root/$directory/$tableName.$ext";
        return $path;
    }

    protected function getItems(): array
    {
        $store = new StoreFile($this->fileName());
        return $store->load() ?: [];
    }

    protected function setItems(array $items)
    {
        $store = new StoreFile($this->fileName());
        $store->save($items);
    }
}
