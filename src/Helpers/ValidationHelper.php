<?php

namespace ZnCore\Domain\Helpers;

use Illuminate\Support\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolationList;
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

    public static function validate2222(ValidateEntityInterface $data): ConstraintViolationList
    {
        $rules = $data->validationRules();
        $validator = SymfonyValidationHelper::createValidator();
        $constraints = new Assert\Collection($rules);
        $violations = $validator->validate(EntityHelper::toArray($data), $constraints);
        return $violations;
    }

    public static function createErrorCollectionFromViolationList(ConstraintViolationList $violations): Collection
    {
        $collection = new Collection;
        foreach ($violations as $violation) {
            $name = trim($violation->getPropertyPath(), '[]');
            $entity = new ValidateErrorEntity;
            $entity->setField($name);
            $entity->setMessage($violation->getMessage());
            $entity->setViolation($violation);
            $collection->add($entity);
        }
        return $collection;
    }
}
