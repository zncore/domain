<?php

namespace ZnCore\Domain\Helpers;

use Illuminate\Container\Container;
use Illuminate\Support\Collection;
use Psr\Container\ContainerInterface;
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ValidatorBuilder;
use Symfony\Contracts\Translation\TranslatorInterface;
use ZnCore\Base\Helpers\EnvHelper;
use ZnCore\Base\Helpers\StringHelper;
use ZnCore\Base\Legacy\Yii\Helpers\ArrayHelper;
use ZnCore\Base\Legacy\Yii\Helpers\Inflector;
use ZnCore\Base\Libs\I18Next\Exceptions\NotFoundBundleException;
use ZnCore\Base\Libs\I18Next\Facades\I18Next;
use ZnCore\Domain\Entities\ValidateErrorEntity;
use ZnCore\Domain\Exceptions\UnprocessibleEntityException;
use ZnCore\Domain\Interfaces\Entity\ValidateEntityInterface;
use ZnLib\Web\Symfony4\MicroApp\ContainerHelper;

class ValidationHelper
{

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
            $validator = self::createValidator();
//
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

    private static function createValidator(): ValidatorInterface
    {
        $container = ContainerHelper::getContainer();
        if($container instanceof ContainerInterface && $container->has(TranslatorInterface::class)) {
            $validatorBuilder = $container->get(ValidatorBuilder::class);
            $translator = $container->get(TranslatorInterface::class);
            $validatorBuilder->setTranslator($translator);
            $validator = $validatorBuilder->getValidator();
        } else {
            $validator = Validation::createValidator();
        }
        return $validator;
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
                $message = $violation->getMessage();
                $entity->setMessage($message);
                $entity->setViolation($violation);
                $collection->add($entity);
            }
        }
        return $collection;
    }
}