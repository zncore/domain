<?php

namespace ZnCore\Domain\Helpers;

use Illuminate\Support\Collection;
use ZnCore\Base\Legacy\Yii\Helpers\ArrayHelper;
use ZnCore\Domain\Entities\ValidateErrorEntity;
use ZnCore\Domain\Exceptions\UnprocessibleEntityException;

class UnprocessableHelper
{

    public static function throwItem(string $field, string $mesage): void
    {
        $errorCollection = new Collection();
        $validateErrorEntity = new ValidateErrorEntity($field, $mesage);
        $errorCollection->add($validateErrorEntity);
        throw new UnprocessibleEntityException($errorCollection);
    }

    public static function throwItems(array $errorArray): void
    {
        $errorCollection = self::generateErrorCollectionFromArray($errorArray);
        throw new UnprocessibleEntityException($errorCollection);
    }

    public static function generateErrorCollectionFromArray(array $errorArray): Collection
    {
        $errorCollection = new Collection;
        foreach ($errorArray as $field => $message) {
            if (is_array($message)) {
                if (ArrayHelper::isAssociative($message)) {
                    $validateErrorEntity = new ValidateErrorEntity($message['field'], $message['message']);
                } else {
                    foreach ($message as $m) {
                        $validateErrorEntity = new ValidateErrorEntity($field, $m);
                        $errorCollection->add($validateErrorEntity);
                    }
                }
            } else {
                $validateErrorEntity = new ValidateErrorEntity($field, $message);
                $errorCollection->add($validateErrorEntity);
            }
        }
        return $errorCollection;
    }
}
