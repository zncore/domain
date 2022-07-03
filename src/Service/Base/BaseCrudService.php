<?php

namespace ZnCore\Domain\Service\Base;

use ZnCore\Domain\Collection\Interfaces\Enumerable;
use ZnCore\Contract\Common\Exceptions\InvalidMethodParameterException;
use ZnCore\Domain\Domain\Traits\DispatchEventTrait;
use ZnCore\Domain\Entity\Exceptions\NotFoundException;
use ZnCore\Base\Instance\Helpers\ClassHelper;
use ZnCore\Domain\Entity\Interfaces\EntityIdInterface;
use ZnCore\Domain\QueryFilter\Interfaces\ForgeQueryByFilterInterface;
use ZnCore\Domain\Domain\Enums\EventEnum;
use ZnCore\Domain\Domain\Events\EntityEvent;
use ZnCore\Domain\Domain\Events\QueryEvent;
use ZnCore\Domain\Entity\Helpers\EntityHelper;
use ZnCore\Base\Validation\Helpers\ValidationHelper;
use ZnCore\Domain\Entity\Interfaces\UniqueInterface;
use ZnCore\Domain\Repository\Interfaces\CrudRepositoryInterface;
use ZnCore\Domain\Domain\Traits\ForgeQueryTrait;
use ZnCore\Domain\Service\Interfaces\CrudServiceInterface;
use ZnCore\Domain\DataProvider\Libs\DataProvider;
use ZnCore\Domain\Query\Entities\Query;
use ZnCore\Domain\Service\Traits\CrudServiceCreateTrait;
use ZnCore\Domain\Service\Traits\CrudServiceDeleteTrait;
use ZnCore\Domain\Service\Traits\CrudServiceFindAllTrait;
use ZnCore\Domain\Service\Traits\CrudServiceFindOneTrait;
use ZnCore\Domain\Service\Traits\CrudServiceUpdateTrait;

/**
 * @method CrudRepositoryInterface getRepository()
 */
abstract class BaseCrudService extends BaseService implements CrudServiceInterface, ForgeQueryByFilterInterface
{

    use DispatchEventTrait;
    use ForgeQueryTrait;

    use CrudServiceCreateTrait;
    use CrudServiceDeleteTrait;
    use CrudServiceFindAllTrait;
    use CrudServiceFindOneTrait;
    use CrudServiceUpdateTrait;

    public function forgeQueryByFilter(object $filterModel, Query $query)
    {
        $repository = $this->getRepository();
        ClassHelper::checkInstanceOf($repository, ForgeQueryByFilterInterface::class);
        $event = new QueryEvent($query);
        $event->setFilterModel($filterModel);
        $this->getEventDispatcher()->dispatch($event, EventEnum::BEFORE_FORGE_QUERY_BY_FILTER);
        $repository->forgeQueryByFilter($filterModel, $query);
    }

    /**
     * @param $id
     * @param Query|null $query
     * @return object|EntityIdInterface
     * @throws NotFoundException
     */
    public function persist(object $entity)
    {
        ValidationHelper::validateEntity($entity);
        $this->getEntityManager()->persist($entity);
    }
}
