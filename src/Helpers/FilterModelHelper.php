<?php

namespace ZnCore\Domain\Helpers;

use ZnCore\Base\Legacy\Yii\Helpers\ArrayHelper;
use ZnCore\Domain\Entities\Query\Where;
use ZnCore\Domain\Exceptions\BadFilterValidateException;
use ZnCore\Domain\Exceptions\UnprocessibleEntityException;
use ZnCore\Domain\Interfaces\Filter\DefaultSortInterface;
use ZnCore\Domain\Libs\Query;

class FilterModelHelper
{

    public static function validate(object $filterModel)
    {
        try {
            ValidationHelper::validateEntity($filterModel);
        } catch (UnprocessibleEntityException $e) {
            $exception = new BadFilterValidateException();
            $exception->setErrorCollection($e->getErrorCollection());
            throw new $exception;
        }
    }

    public static function forgeCondition(Query $query, object $filterModel) {
        $params = EntityHelper::toArrayForTablize($filterModel);
        foreach ($params as $paramsName => $paramValue) {
            if ($paramValue !== null) {
                $query->whereNew(new Where($paramsName, $paramValue));
            }
        }
    }

    public static function forgeOrder(Query $query, object $filterModel) {
        $sort = $query->getParam(Query::ORDER);
        if(empty($sort) && $filterModel instanceof DefaultSortInterface) {
            $sort = $filterModel->defaultSort();
            $query->orderBy($sort);
        }
    }

    public static function forgeQueryByFilter(Query $query, object $filterModel)
    {
        self::validate($filterModel);
        self::forgeCondition($query, $filterModel);
        self::forgeOrder($query, $filterModel);
    }
}
