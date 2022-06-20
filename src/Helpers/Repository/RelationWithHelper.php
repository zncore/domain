<?php

namespace ZnCore\Domain\Helpers\Repository;

use ZnCore\Base\Helpers\DeprecateHelper;
use ZnCore\Domain\Libs\Query;

DeprecateHelper::hardThrow();

class RelationWithHelper
{

    public static function fetch(Query $query, array &$withTrimmedArray = []): array
    {
        $withArray = $query->getWith();
        if (empty($withArray)) {
            return [];
        }
        $withArray = self::sortWithParam($withArray);
        $fields = [];
        foreach ($withArray as $with) {
            $dotPos = strpos($with, '.');
            if ($dotPos !== false) {
                $withTrimmed = substr($with, $dotPos + 1);
                $fieldName = substr($with, 0, $dotPos);
            } else {
                $withTrimmed = null;
                $fieldName = $with;
            }
            if ( ! empty($fieldName)) {
                $fields[] = $fieldName;
            }
            if ( ! empty($withTrimmed)) {
                $withTrimmedArray[$fieldName][] = $withTrimmed;
            } else {
                $withTrimmedArray[$fieldName] = [];
            }

        }
        $fields = array_unique($fields);
        return $fields;
    }

    private static function sortWithParam(array $withArray): array
    {
        $withArray = array_unique($withArray);
        usort($withArray, [\ZnCore\Base\Legacy\Yii\Helpers\ArrayHelper::class, 'sortByLen']);
        $withArray = array_reverse($withArray);
        return $withArray;
    }

    private static function extractName(string $w): string
    {
        $dotPos = strpos($w, '.');
        if ($dotPos !== false) {
            $w1 = substr($w, 0, $dotPos);
        } else {
            $w1 = $w;
        }
        return $w1;
    }
}
