<?php

namespace ZnCore\Domain\Constraints;

use Exception;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use ZnBundle\Reference\Domain\Entities\ItemEntity;
use ZnBundle\Reference\Domain\Interfaces\Repositories\ItemRepositoryInterface;
use ZnCore\Base\Exceptions\NotFoundException;
use ZnCore\Base\Libs\Container\Helpers\ContainerHelper;
use ZnCore\Base\Libs\EntityManager\Interfaces\EntityManagerInterface;
use ZnCore\Domain\Interfaces\ReadAllInterface;
use ZnCore\Base\Libs\Query\Entities\Query;

class RelationValidator extends BaseValidator
{

    protected $constraintClass = Relation::class;

    public function validate($value, Constraint $constraint)
    {
        /*if (!$constraint instanceof Relation) {
            throw new UnexpectedTypeException($constraint, Relation::class);
        }

        // custom constraints should ignore null and empty values to allow
        // other constraints (NotBlank, NotNull, etc.) to take care of that
        if (empty($value)) {
            return;
        }*/

        $this->checkConstraintType($constraint);
        if (empty($value)) {
            return;
        }

        if (!is_numeric($value)) {
            // throw this exception if your validator cannot handle the passed type so that it can be marked as invalid
            throw new UnexpectedValueException($value, 'int');
        }

        /** @var EntityManagerInterface $em */
        $em = ContainerHelper::getContainer()->get(EntityManagerInterface::class);
        /** @var ReadAllInterface $repository */
        $repository = $em->getRepositoryByEntityClass($constraint->foreignEntityClass);

        try {
            $repository->oneById($value);
        } catch (NotFoundException $e) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        } catch (Exception $e) {
            if ($constraint->message) {
                $message = $constraint->message;
            } else {
                $message = $e->getMessage();
            }
            $this->context->buildViolation($message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}
