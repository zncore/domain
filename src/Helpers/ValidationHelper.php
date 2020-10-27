<?php

namespace ZnCore\Domain\Helpers;

use Illuminate\Support\Collection;
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validation;
use ZnCore\Domain\Entities\ValidateErrorEntity;
use ZnCore\Domain\Exceptions\UnprocessibleEntityException;
use ZnCore\Domain\Interfaces\Entity\ValidateEntityInterface;

class ValidationHelper
{

    public static function throwUnprocessable(array $errorArray)
    {
        $errorCollection = ValidationHelper::generateErrorCollectionFromArray($errorArray);
        $exception = new UnprocessibleEntityException;
        $exception->setErrorCollection($errorCollection);
        throw $exception;
    }

    public static function collectionToArray(Collection $errorCollection): array {
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
                foreach ($message as $m) {
                    $validateErrorEntity = new ValidateErrorEntity;
                    $validateErrorEntity->setField($field);
                    $validateErrorEntity->setMessage($m);
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

    public static function validateEntity(ValidateEntityInterface $entity): void
    {
        $rules = $entity->validationRules();
        $errorCollection = self::validate($rules, $entity);
        if ($errorCollection->count() > 0) {
            $exception = new UnprocessibleEntityException;
            $exception->setErrorCollection($errorCollection);
            throw $exception;
        }
    }

    /**
     * @return array | Collection | ValidateErrorEntity[]
     */
    public static function validate($rules, $data): Collection
    {
        $violations = [];
        if (!empty($rules)) {
            $validator = Validation::createValidator();
            $propertyAccessor = PropertyAccess::createPropertyAccessor();
            foreach ($rules as $name => $rule) {
                $value = $propertyAccessor->getValue($data, $name);
                $vol = $validator->validate($value, $rules[$name]);
                if ($vol->count()) {
                    $violations[$name] = $vol;
                }
            }
        }
        return self::prepareUnprocessible($violations);
    }

    /**
     * @param array | ConstraintViolationList[] $violations
     * @return  array | Collection | ValidateErrorEntity[]
     */
    private static function prepareUnprocessible(array $violations): Collection
    {
        $collection = new Collection;
        foreach ($violations as $name => $violationList) {
            foreach ($violationList as $violation) {
                $violation->getCode();
                $entity = new ValidateErrorEntity;
                $entity->setField($name);
                $entity->setMessage($violation->getMessage());
                $entity->setViolation($violation);
                $collection->add($entity);
            }
        }
        return $collection;
    }

}