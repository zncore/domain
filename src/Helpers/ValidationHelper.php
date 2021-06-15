<?php

namespace ZnCore\Domain\Helpers;

use Illuminate\Support\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
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

    public static function validateDynamicEntity(ValidateEntityInterface $entity): ConstraintViolationList
    {
        DeprecateHelper::softThrow();
        $rules = $entity->validationRules();
        $validator = SymfonyValidationHelper::createValidator();
        $constraints = new Assert\Collection($rules);
        $violations = $validator->validate(EntityHelper::toArray($entity), $constraints);
        $newViolationArray = [];
        foreach ($violations as $violation) {
            /** @var $violation ConstraintViolation */
            $name = trim($violation->getPropertyPath(), '[]');
            $newViolationArray[] = new ConstraintViolation($violation->getMessage(), $violation->getMessageTemplate(), $violation->getParameters(), $violation->getRoot(), $name, $violation->getInvalidValue(), $violation->getPlural(), $violation->getCode(), $violation->getConstraint(), $violation->getCause());
        }
        return new ConstraintViolationList($newViolationArray);
    }

    public static function validate2222(ValidateEntityInterface $data): ConstraintViolationList
    {
        DeprecateHelper::softThrow();
        $rules = $data->validationRules();
        $validator = SymfonyValidationHelper::createValidator();
        $constraints = new Assert\Collection($rules);

        /*$constraint = new Assert\All([
            'constraints' => [
                $constraints
            ],
            'payload' => EntityHelper::toArray($data),
        ]);

        $violations = $validator->validate($constraint);*/

        /*ValidateObjectEntity::configValidatorMetadata($rules);

        $entity = new ValidateObjectEntity();
        $attrs = array_merge(array_keys($rules), array_keys(EntityHelper::toArray($data)));
        $attrs = array_unique($attrs);
        $entity->configAttributes($attrs);

        EntityHelper::setAttributes($entity, EntityHelper::toArray($data));

        $violations = $validator->validate($entity);
        return $violations;*/

        $violations = $validator->validate(EntityHelper::toArray($data), $constraints);
        return $violations;
    }

    public static function createErrorCollectionFromViolationList(ConstraintViolationList $violations): Collection
    {
        DeprecateHelper::softThrow();
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
