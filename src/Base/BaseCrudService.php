<?php

namespace ZnCore\Domain\Base;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use ZnCore\Base\Exceptions\NotFoundException;
use ZnCore\Base\Helpers\ClassHelper;
use ZnCore\Base\Libs\App\Helpers\ContainerHelper;
use ZnCore\Domain\Entities\EventEntity;
use ZnCore\Domain\Enums\EventEnum;
use ZnCore\Domain\Events\EntityEvent;
use ZnCore\Domain\Helpers\EntityHelper;
use ZnCore\Domain\Helpers\ValidationHelper;
use ZnCore\Domain\Interfaces\Entity\EntityIdInterface;
use ZnCore\Domain\Interfaces\Entity\ValidateEntityInterface;
use ZnCore\Domain\Interfaces\ForgeQueryByFilterInterface;
use ZnCore\Domain\Interfaces\Repository\CrudRepositoryInterface;
use ZnCore\Domain\Interfaces\Service\CrudServiceInterface;
use ZnCore\Domain\Libs\DataProvider;
use ZnCore\Domain\Libs\Query;

/**
 * @method CrudRepositoryInterface getRepository()
 */
abstract class BaseCrudService extends BaseService implements CrudServiceInterface, ForgeQueryByFilterInterface
{

    /** @var EventDispatcher */
    private $eventDispatcher;

    private $subscriberClasseList = [];

    public function initEvent()
    {
        if ($this->eventDispatcher == null) {
            $this->eventDispatcher = new EventDispatcher();
        }
        if ($this->subscriberClasseList) {
            foreach ($this->subscriberClasseList as $index => $subscriberClass) {
                $subscriberInstance = ContainerHelper::getContainer()->get($subscriberClass);
                $this->eventDispatcher->addSubscriber($subscriberInstance);
                unset($this->subscriberClasseList[$index]);
            }
        }
    }

    public function getEventDispatcher(): EventDispatcherInterface
    {
        $this->initEvent();
        return $this->eventDispatcher;
    }

    public function addListener(string $eventName, $listener, int $priority = 0)
    {
        $this->initEvent();
        $this->getEventDispatcher()->addListener($eventName, $listener, $priority);
    }

    public function addSubscriber(EventSubscriberInterface $subscriber)
    {
        $this->initEvent();
        $this->getEventDispatcher()->addSubscriber($subscriber);
    }

    public function addSubscriberClass(string $subscriberClass)
    {
        $this->subscriberClasseList[] = $subscriberClass;
    }

    public function beforeMethod(string $method)
    {
        return true;
    }

    public function afterMethod(string $method, EventEntity $event)
    {
        $event->setMethod($method);
        $event->setTarget($this);
        $event->setType('after');
    }

    protected function forgeQuery(Query $query = null)
    {
        $query = Query::forge($query);
        return $query;
    }

    public function forgeQueryByFilter(ValidateEntityInterface $filterModel, Query $query = null)
    {
        $query = $this->forgeQuery($query);
        $repository = $this->getRepository();
        ClassHelper::isInstanceOf($repository, ForgeQueryByFilterInterface::class);
        $repository->forgeQueryByFilter($filterModel, $query);
    }

    public function getDataProvider(Query $query = null): DataProvider
    {
        $dataProvider = new DataProvider($this, $query);
        return $dataProvider;
    }

    public function all(Query $query = null)
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
    public function oneById($id, Query $query = null)
    {
        $query = $this->forgeQuery($query);
        $isAvailable = $this->beforeMethod('oneById');
        $entity = $this->getRepository()->oneById($id, $query);

        $event = new EntityEvent($entity);
        $this->getEventDispatcher()->dispatch($event, EventEnum::AFTER_READ_ENTITY);

        return $entity;
    }

    public function persist(object $entity)
    {
        ValidationHelper::validateEntity($entity);
        $this->getRepository()->create($entity);
    }

    public function create($data): EntityIdInterface
    {
        $isAvailable = $this->beforeMethod('create');
        $entityClass = $this->getEntityClass();

        $entity = new $entityClass;
        EntityHelper::setAttributes($entity, $data);

        $event = new EntityEvent($entity);
        $this->getEventDispatcher()->dispatch($event, EventEnum::BEFORE_CREATE_ENTITY);
        if ($event->isPropagationStopped()) {
            return $entity;
        }

        ValidationHelper::validateEntity($entity);
        $this->getRepository()->create($entity);
        $event = new EventEntity();
        $event->setData($entity);
        $this->afterMethod('create', $event);

        $event = new EntityEvent($entity);
        $this->getEventDispatcher()->dispatch($event, EventEnum::AFTER_CREATE_ENTITY);

        return $entity;
    }

    public function updateById($id, $data)
    {
        $isAvailable = $this->beforeMethod('updateById');
        if (!$isAvailable) {
            return;
        }
        //$entityClass = $this->getEntityClass();
        //$entityInstance = new $entityClass;
        /** @var ValidateEntityInterface $entityInstance */
        $entityInstance = $this->oneById($id);
        EntityHelper::setAttributes($entityInstance, $data);
        //ValidationHelper::validateEntity($entityInstance);
        //dd($entityInstance);
        //$entityInstance->setId($id);
        return $this->getRepository()->update($entityInstance);
    }

    public function deleteById($id)
    {
        $isAvailable = $this->beforeMethod('deleteById');
        $entity = $this->getRepository()->oneById($id);
        $id = $this->getRepository()->deleteById($id);

        $event = new EntityEvent($entity);
        $this->getEventDispatcher()->dispatch($event, EventEnum::AFTER_DELETE_ENTITY);

        return $id;
    }
}
