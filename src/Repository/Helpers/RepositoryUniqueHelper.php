<?php

namespace ZnCore\Domain\Repository\Helpers;

use ZnCore\Text\Helpers\Inflector;
use ZnCore\Entity\Helpers\EntityHelper;
use ZnCore\Entity\Interfaces\UniqueInterface;
use ZnCore\Domain\Query\Entities\Query;

class RepositoryUniqueHelper
{

    public static function buildQuery(UniqueInterface $entity, array $uniqueConfig): Query
    {
        $query = new Query();
        foreach ($uniqueConfig as $uniqueName) {
            $value = EntityHelper::getValue($entity, $uniqueName);
            if ($value === null) {
                return null;
            }
            $query->where(Inflector::underscore($uniqueName), $value);
        }
        return $query;
    }
}
