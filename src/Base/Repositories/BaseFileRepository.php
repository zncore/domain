<?php

namespace ZnCore\Domain\Base\Repositories;

use ZnCore\Base\Exceptions\NotImplementedMethodException;
use ZnCore\Base\Legacy\Yii\Helpers\FileHelper;
use ZnCore\Base\Libs\DotEnv\DotEnv;
use ZnCore\Base\Libs\Store\StoreFile;
use ZnCore\Domain\Interfaces\GetEntityClassInterface;
use ZnCore\Domain\Interfaces\Libs\EntityManagerInterface;
use ZnCore\Domain\Interfaces\Repository\RepositoryInterface;
use ZnCore\Domain\Traits\EntityManagerTrait;

abstract class BaseFileRepository implements RepositoryInterface/*, GetEntityClassInterface*/
{

    use EntityManagerTrait;

    public function __construct(EntityManagerInterface $em)
    {
        $this->setEntityManager($em);
    }

    //abstract public function getEntityClass(): string;

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
        // todo: cache data
        $store = new StoreFile($this->fileName());
        return $store->load() ?: [];
    }

    protected function setItems(array $items)
    {
        $store = new StoreFile($this->fileName());
        return $store->save($items);
    }
}
