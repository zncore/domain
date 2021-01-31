<?php

namespace ZnCore\Domain\Helpers;

use Illuminate\Support\Collection;
use Psr\Container\ContainerInterface;
use Symfony\Component\PropertyAccess\Exception\UninitializedPropertyException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Mapping\Loader\StaticMethodLoader;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ValidatorBuilder;
use Symfony\Contracts\Translation\TranslatorInterface;
use ZnCore\Base\Legacy\Yii\Helpers\ArrayHelper;
use ZnCore\Base\Libs\App\Helpers\ContainerHelper;
use ZnCore\Base\Libs\I18Next\Facades\I18Next;
use ZnCore\Base\Libs\I18Next\SymfonyTranslation\Helpers\TranslatorHelper;
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
        $validator = self::createValidator();
        if (!empty($rules)) {

            $propertyAccessor = PropertyAccess::createPropertyAccessor();

            foreach ($rules as $name => $rule) {
                try {
                    $value = $propertyAccessor->getValue($data, $name);
                } catch (UninitializedPropertyException $e) {
                    $value = null;
                }
                $vol = $validator->validate($value, $rules[$name]);
                if ($vol->count()) {
                    $violations[$name] = $vol;
                }
            }
            return self::prepareUnprocessible($violations);
        } else {

            /*$metadata = new ClassMetadata(get_class($data));
            $loader = new StaticMethodLoader();
            $loader->loadClassMetadata($metadata);*/

            $validator = ContainerHelper::getContainer()->get(ValidatorBuilder::class)
//            $validator = Validation::createValidatorBuilder()

                ->addMethodMapping('loadValidatorMetadata')
                ->getValidator();

            /** @var ConstraintViolationList $violationsList */
            $violationsList = $validator->validate($data);
            //dd($violationsList);
            if ($violationsList->count()) {
                $violations = (array)$violationsList->getIterator();
                //dd($violations);
//                dd();
            }
            return self::prepareUnprocessible2($violationsList);
        }
    }

    private static function createValidator(): ValidatorInterface
    {
        $container = ContainerHelper::getContainer();



        if ($container instanceof ContainerInterface && $container->has(TranslatorInterface::class)) {
            $validatorBuilder = $container->get(ValidatorBuilder::class);
            $translator = $container->get(TranslatorInterface::class);
            $validatorBuilder->setTranslator($translator);
            $validator = $validatorBuilder->getValidator();
        } else {
            //$validator = Validation::createValidator();
            $validator = ContainerHelper::getContainer()->get(ValidatorBuilder::class)
            //$validator = Validation::createValidatorBuilder()
                ->addMethodMapping('loadValidatorMetadata')
                ->getValidator();
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
                //$name = $violation->propertyPath();
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

    private static function prepareUnprocessible2(ConstraintViolationList $violationList): Collection
    {
//        dd($violationList);
        $collection = new Collection;
        foreach ($violationList->getIterator() as $violation) {
            $name = $violation->getPropertyPath();

            $violation->getCode();
            $entity = new ValidateErrorEntity;
            $entity->setField($name);
            $message = $violation->getMessage();

            //$entity->setMessage($message);
            $id = $violation->getMessageTemplate();
            $parametersI18Next = TranslatorHelper::paramsToI18Next($violation->getParameters());
            $id = TranslatorHelper::getSingularFromId($id);
            $key = 'message.' . TranslatorHelper::messageToHash($id);
            $transtatedMessage = I18Next::t('symfony', $key, $parametersI18Next);
            if($transtatedMessage == $key) {
                $entity->setMessage($message);
            } else {
                $entity->setMessage($transtatedMessage);
            }

            $entity->setViolation($violation);
            $collection->add($entity);
        }
        return $collection;
    }
}