<?php

namespace ZnCore\Domain\Helpers\Repository;

use Illuminate\Support\Collection;
use ZnCore\Base\Legacy\Yii\Helpers\ArrayHelper;
use php7rails\domain\interfaces\services\ReadAllInterface;
use ZnCore\Domain\Libs\Query;
use ZnCore\Domain\Entities\relation\BaseForeignEntity;
//use ZnCore\Domain\Enums\RelationClassTypeEnum;

class RelationRepositoryHelper
{

    public static function getAll(BaseForeignEntity $relationConfig, Query $query = null): Collection
    {
        $query = Query::forge($query);
        /** @var ReadAllInterface $repository */
        $repository = $relationConfig->model;
        //dd($query);
        //$repository = self::getInstance($relationConfig);
        return $repository->all($query);
    }

    /*private static function getInstance(BaseForeignEntity $relationConfigForeign): object
    {
        $domainInstance = \App::$domain->get($relationConfigForeign->domain);
        if ($relationConfigForeign->classType == RelationClassTypeEnum::SERVICE) {
            $locator = $domainInstance;
        } else {
            $locator = $domainInstance->repositories;
        }
        return ArrayHelper::getValue($locator, $relationConfigForeign->name);
    }*/

}
