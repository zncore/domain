<?php

namespace ZnCore\Domain\Helpers;

use Illuminate\Support\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use ZnBundle\Eav\Domain\Entities\DynamicEntity;
use ZnBundle\Eav\Domain\Entities\EntityEntity;
use ZnBundle\Eav\Domain\Libs\TypeNormalizer;
use ZnBundle\Eav\Domain\Libs\Validator;
use ZnCore\Base\Helpers\DeprecateHelper;
use ZnCore\Base\Legacy\Yii\Helpers\ArrayHelper;
use ZnCore\Base\Libs\DynamicEntity\Helpers\DynamicEntityValidationHelper;
use ZnCore\Domain\Entities\ValidateErrorEntity;
use ZnCore\Domain\Exceptions\UnprocessibleEntityException;
use ZnCore\Base\Libs\Entity\Interfaces\ValidateEntityByMetadataInterface;
use ZnCore\Base\Libs\DynamicEntity\Interfaces\ValidateDynamicEntityInterface;

class UnprocessableHelper
{

    public static function throwUnprocessableItem(string $field, string $mesage)
    {
        $errorCollection = new Collection;
        $validateErrorEntity = new ValidateErrorEntity;
        $validateErrorEntity->setField($field);
        $validateErrorEntity->setMessage($mesage);
        $errorCollection->add($validateErrorEntity);
        $exception = new UnprocessibleEntityException;
        $exception->setErrorCollection($errorCollection);
        throw $exception;
    }

    public static function throwUnprocessable(array $errorArray)
    {
        $errorCollection = self::generateErrorCollectionFromArray($errorArray);
        $exception = new UnprocessibleEntityException;
        $exception->setErrorCollection($errorCollection);
        throw $exception;
    }

    public static function generateErrorCollectionFromArray(array $errorArray): Collection
    {
        $errorCollection = new Collection;
        foreach ($errorArray as $field => $message) {
            if (is_array($message)) {
                if (ArrayHelper::isAssociative($message)) {
                    $validateErrorEntity = new ValidateErrorEntity;
                    $validateErrorEntity->setField($message['field']);
                    $validateErrorEntity->setMessage($message['message']);
                } else {
                    foreach ($message as $m) {
                        $validateErrorEntity = new ValidateErrorEntity;
                        $validateErrorEntity->setField($field);
                        $validateErrorEntity->setMessage($m);
                    }
                }
            } else {
                $validateErrorEntity = new ValidateErrorEntity;
                $validateErrorEntity->setField($field);
                $validateErrorEntity->setMessage($message);
            }
        }
        $errorCollection->add($validateErrorEntity);
        return $errorCollection;
    }
}
