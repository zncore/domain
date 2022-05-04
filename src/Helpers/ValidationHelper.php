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
use ZnCore\Domain\Entities\ValidateErrorEntity;
use ZnCore\Domain\Exceptions\UnprocessibleEntityException;
use ZnCore\Domain\Interfaces\Entity\ValidateEntityInterface;

class ValidationHelper
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
        $errorCollection = ValidationHelper::generateErrorCollectionFromArray($errorArray);
        $exception = new UnprocessibleEntityException;
        $exception->setErrorCollection($errorCollection);
        throw $exception;
    }

    public static function collectionToArray(Collection $errorCollection): array
    {
        $array = [];
        /** @var ValidateErrorEntity $validateErrorEntity */
        foreach ($errorCollection as $validateErrorEntity) {
            $array[] = [
                'field' => $validateErrorEntity->getField(),
                'message' => $validateErrorEntity->getMessage(),
            ];
        }
        return $array;
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

    public static function validateEntity(object $entity): void
    {
        $errorCollection = self::validate($entity);
        if ($errorCollection->count() > 0) {
            $exception = new UnprocessibleEntityException;
            $exception->setErrorCollection($errorCollection);
            throw $exception;
        }
    }

    /**
     * @return array | Collection | ValidateErrorEntity[]
     */
    public static function validate($data): Collection
    {
        if ($data instanceof ValidateEntityInterface) {
            return ArrayValidationHelper::validate($data);
        } else {
            return SymfonyValidationHelper::validate($data);
        }
    }

    /**
     * @return array | Collection | ValidateErrorEntity[]
     */
    public static function validateValue($value, array $rules): ConstraintViolationList
    {
        $validator = SymfonyValidationHelper::createValidator();
        $violations = $validator->validate($value, $rules);
        return $violations;
    }

    public static function validateDynamicEntity2(object $dynamicEntity, EntityEntity $entityEntity, array $data): void
    {
        $normalizer = new TypeNormalizer();
        $data = $normalizer->normalizeData($data, $entityEntity);
        EntityHelper::setAttributes($dynamicEntity, $data);
        $validator = new Validator();
        $validator->validate($data, $dynamicEntity->validationRules());
        //return $dynamicEntity;
    }
}
