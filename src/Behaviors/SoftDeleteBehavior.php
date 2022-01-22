<?php

namespace ZnCore\Domain\Behaviors;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use ZnCore\Base\Enums\StatusEnum;
use ZnCore\Domain\Enums\EventEnum;
use ZnCore\Domain\Events\EntityEvent;
use ZnCore\Domain\Events\QueryEvent;
use ZnCore\Domain\Interfaces\Libs\EntityManagerInterface;
use ZnCore\Domain\Traits\EntityManagerTrait;

class SoftDeleteBehavior implements EventSubscriberInterface
{

    use EntityManagerTrait;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->setEntityManager($entityManager);
    }

    public static function getSubscribedEvents()
    {
        return [
            EventEnum::BEFORE_DELETE_ENTITY => 'onBeforeDelete',
            EventEnum::BEFORE_FORGE_QUERY => 'onForgeQuery',
        ];
    }

    public function onBeforeDelete(EntityEvent $event)
    {
        $entity = $event->getEntity();
        if (method_exists($entity, 'delete')) {
            $entity->delete();
        } else {
            $entity->setStatusId(StatusEnum::DELETED);
        }
        $this->getEntityManager()->persist($entity);
        $event->skipHandle();
    }

    public function onForgeQuery(QueryEvent $event)
    {
        $event->getQuery()->where('status_id', StatusEnum::ENABLED);
    }
}
