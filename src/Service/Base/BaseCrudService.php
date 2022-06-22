<?php

namespace ZnCore\Domain\Service\Base;

use Illuminate\Support\Enumerable;
use ZnCore\Base\Exceptions\InvalidMethodParameterException;
use ZnCore\Base\Exceptions\NotFoundException;
use ZnCore\Base\Helpers\ClassHelper;
use ZnCore\Domain\Entity\Interfaces\EntityIdInterface;
use ZnCore\Domain\QueryFilter\Interfaces\ForgeQueryByFilterInterface;
use ZnCore\Domain\Domain\Enums\EventEnum;
use ZnCore\Domain\Domain\Events\EntityEvent;
use ZnCore\Domain\Domain\Events\QueryEvent;
use ZnCore\Domain\Entity\Helpers\EntityHelper;
use ZnCore\Base\Libs\Validation\Helpers\ValidationHelper;
use ZnCore\Domain\Entity\Interfaces\UniqueInterface;
use ZnCore\Domain\Repository\Interfaces\CrudRepositoryInterface;
use ZnCore\Domain\Service\Interfaces\CrudServiceInterface;
use ZnCore\Domain\DataProvider\Libs\DataProvider;
use ZnCore\Domain\Query\Entities\Query;

/**
 * @method CrudRepositoryInterface getRepository()
 */
abstract class BaseCrudService extends BaseService implements CrudServiceInterface, ForgeQueryByFilterInterface
{

    public function beforeMethod(string $method)
    {
        return true;
    }

    /*public function afterMethod(string $method, EventEntity $event)
    {
        $event->setMethod($method);
        $event->setTarget($this);
        $event->setType('after');
    }*/

    protected function dispatchQueryEvent(Query $query, string $eventName): QueryEvent
    {
        $event = new QueryEvent($query);
        $this->getEventDispatcher()->dispatch($event, $eventName);
        return $event;
    }

    protected function forgeQuery(Query $query = null)
    {
        $query = Query::forge($query);
        $this->dispatchQueryEvent($query, EventEnum::BEFORE_FORGE_QUERY);
        return $query;
    }

    public function forgeQueryByFilter(object $filterModel, Query $query)
    {
        $repository = $this->getRepository();
        ClassHelper::checkInstanceOf($repository, ForgeQueryByFilterInterface::class);
        $event = new QueryEvent($query);
        $event->setFilterModel($filterModel);
        $this->getEventDispatcher()->dispatch($event, EventEnum::BEFORE_FORGE_QUERY_BY_FILTER);
        $repository->forgeQueryByFilter($filterModel, $query);
    }

    public function getDataProvider(Query $query = null): DataProvider
    {
        $dataProvider = new DataProvider($this, $query);
        return $dataProvider;
    }

    public function all(Query $query = null): Enumerable
    {
        $isAvailable = $this->beforeMethod('all');
        $query = $this->forgeQuery($query);
        $collection = $this->getRepository()->all($query);
        return $collection;
    }

    public function count(Query $query = null): int
    {
        $isAvailable = $this->beforeMethod('count');
        $query = $this->forgeQuery($query);
        return $this->getRepository()->count($query);
    }

    /**
     * @param $id
     * @param Query|null $query
     * @return object|EntityIdInterface
     * @throws NotFoundException
     */
    public function oneById($id, Query $query = null): EntityIdInterface
    {
        if (empty($id)) {
            throw (new InvalidMethodParameterException('Empty ID'))
                ->setParameterName('id');
        }
        $query = $this->forgeQuery($query);
        $isAvailable = $this->beforeMethod('oneById');
        $entity = $this->getRepository()->oneById($id, $query);
        $event = $this->dispatchEntityEvent($entity, EventEnum::AFTER_READ_ENTITY);
        return $entity;
    }

    public function oneByUnique(UniqueInterface $entity): EntityIdInterface
    {
        return $this->getRepository()->oneByUnique($entity);
    }

    public function persist(object $entity)
    {
        ValidationHelper::validateEntity($entity);
        $this->getEntityManager()->persist($entity);
    }

    protected function dispatchEntityEvent(object $entity, string $eventName): EntityEvent
    {
        $event = new EntityEvent($entity);
        $this->getEventDispatcher()->dispatch($event, $eventName);
        return $event;
    }

    public function create($data): EntityIdInterface
    {
        if ($this->hasEntityManager()) {
            $this->getEntityManager()->beginTransaction();
        }
        try {
            $isAvailable = $this->beforeMethod('create');
            $entityClass = $this->getEntityClass();
            $entity = $this->getEntityManager()->createEntity($this->getEntityClass(), $data);
            $event = $this->dispatchEntityEvent($entity, EventEnum::BEFORE_CREATE_ENTITY);
            if ($event->isPropagationStopped()) {
                return $entity;
            }
            ValidationHelper::validateEntity($entity);
            $this->getRepository()->create($entity);

            // todo: убрать
            /*$event = new EventEntity();
            $event->setData($entity);
            $this->afterMethod('create', $event);*/

            $event = $this->dispatchEntityEvent($entity, EventEnum::AFTER_CREATE_ENTITY);
        } catch (\Throwable $e) {
            if ($this->hasEntityManager()) {
                $this->getEntityManager()->rollbackTransaction();
            }
            throw $e;
        }
        if ($this->hasEntityManager()) {
            $this->getEntityManager()->commitTransaction();
        }
        return $entity;
    }

    public function updateById($id, $data)
    {
        if ($this->hasEntityManager()) {
            $this->getEntityManager()->beginTransaction();
        }
        try {
            $isAvailable = $this->beforeMethod('updateById');
            if (!$isAvailable) {
                return;
            }
            $entity = $this->getRepository()->oneById($id);

            EntityHelper::setAttributes($entity, $data);

            $event = $this->dispatchEntityEvent($entity, EventEnum::BEFORE_UPDATE_ENTITY);
            if ($event->isPropagationStopped()) {
                //return $entity;
            }

            $this->getRepository()->update($entity);

            $event = $this->dispatchEntityEvent($entity, EventEnum::AFTER_UPDATE_ENTITY);
            if ($event->isPropagationStopped()) {
                //return $entity;
            }
        } catch (\Throwable $e) {
            if ($this->hasEntityManager()) {
                $this->getEntityManager()->rollbackTransaction();
            }
            throw $e;
        }
        if ($this->hasEntityManager()) {
            $this->getEntityManager()->commitTransaction();
        }
    }

    public function deleteById($id)
    {
        if ($this->hasEntityManager()) {
            $this->getEntityManager()->beginTransaction();
        }
        try {
            $isAvailable = $this->beforeMethod('deleteById');
            $entity = $this->getRepository()->oneById($id);
            $event = $this->dispatchEntityEvent($entity, EventEnum::BEFORE_DELETE_ENTITY);
            if (!$event->isSkipHandle()) {
                $this->getRepository()->deleteById($id);
            }
            $event = $this->dispatchEntityEvent($entity, EventEnum::AFTER_DELETE_ENTITY);
        } catch (\Throwable $e) {
            if ($this->hasEntityManager()) {
                $this->getEntityManager()->rollbackTransaction();
            }
            throw $e;
        }
        if ($this->hasEntityManager()) {
            $this->getEntityManager()->commitTransaction();
        }
    }
}
