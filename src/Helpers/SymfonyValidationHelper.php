<?php

namespace ZnCore\Domain\Helpers;

use Illuminate\Support\Collection;
use Psr\Container\ContainerInterface;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ValidatorBuilder;
use Symfony\Contracts\Translation\TranslatorInterface;
use ZnCore\Base\Libs\App\Helpers\ContainerHelper;
use ZnCore\Domain\Entities\ValidateErrorEntity;

class SymfonyValidationHelper
{

    /**
     * @return array | Collection | ValidateErrorEntity[]
     */
    public static function validate($data): Collection
    {
        return self::validateByMetadata($data);
    }

    public static function createValidator(): ValidatorInterface
    {
        $validatorBuilder = self::createValidatorBuilder();
        $validator = $validatorBuilder->getValidator();
        return $validator;
//        $container = ContainerHelper::getContainer();
//
//        if ($container instanceof ContainerInterface && $container->has(TranslatorInterface::class)) {
//            /*$validatorBuilder = $container->get(ValidatorBuilder::class);
//            $translator = $container->get(TranslatorInterface::class);
//            $validatorBuilder->setTranslator($translator);
//            $validator = $validatorBuilder->getValidator();*/
//            $validatorBuilder = self::createValidatorBuilder();
//
//        } else {
//            //$validator = Validation::createValidator();
//            $validator = ContainerHelper::getContainer()->get(ValidatorBuilder::class)
//                //$validator = Validation::createValidatorBuilder()
//                ->addMethodMapping('loadValidatorMetadata')
//                ->getValidator();
//        }
    }

    private static function createValidatorBuilder(): ValidatorBuilder
    {
        $container = ContainerHelper::getContainer();
        $validatorBuilder = $container->get(ValidatorBuilder::class);
        $validatorBuilder->addMethodMapping('loadValidatorMetadata');
        if ($container instanceof ContainerInterface && $container->has(TranslatorInterface::class)) {
            $translator = $container->get(TranslatorInterface::class);
        } else {
            $translator = new Translator('en');
        }
        $validatorBuilder->setTranslator($translator);
        return $validatorBuilder;
    }

    private static function validateByMetadata(object $entity) {
//        $validatorBuilder = self::createValidatorBuilder();
//        $validator = $validatorBuilder->getValidator();
        $validator = self::createValidator();
        /** @var ConstraintViolationList $violationsList */
        $violationsList = $validator->validate($entity);
        if ($violationsList->count()) {
            $violations = (array)$violationsList->getIterator();
        }
        return self::prepareUnprocessible2($violationsList);
    }

    private static function prepareUnprocessible2(ConstraintViolationList $violationList): Collection
    {
        $collection = new Collection;
        foreach ($violationList->getIterator() as $violation) {
            $name = $violation->getPropertyPath();

            $violation->getCode();
            $entity = new ValidateErrorEntity;
            $entity->setField($name);
            $message = $violation->getMessage();

            /*$id = $violation->getMessageTemplate();
            $parametersI18Next = TranslatorHelper::paramsToI18Next($violation->getParameters());
            $id = TranslatorHelper::getSingularFromId($id);
            $key = 'message.' . TranslatorHelper::messageToHash($id);
            $transtatedMessage = I18Next::t('symfony', $key, $parametersI18Next);
            if ($transtatedMessage == $key) {
                //$entity->setMessage($message);
            } else {
//                $entity->setMessage($transtatedMessage);
                $message = $transtatedMessage;
            }*/
            $entity->setMessage($message);
            $entity->setViolation($violation);
            $collection->add($entity);
        }
        return $collection;
    }
}