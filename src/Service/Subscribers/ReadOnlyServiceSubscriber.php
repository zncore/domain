<?php

namespace ZnCore\Domain\Service\Subscribers;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use ZnCore\Base\ReadOnly\Exceptions\ReadOnlyException;
use ZnCore\Domain\Domain\Enums\EventEnum;
use ZnCore\Domain\Domain\Events\EntityEvent;
use ZnCore\Domain\EntityManager\Interfaces\EntityManagerInterface;
use ZnCore\Domain\EntityManager\Traits\EntityManagerAwareTrait;

class ReadOnlyServiceSubscriber implements EventSubscriberInterface
{

    use EntityManagerAwareTrait;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->setEntityManager($entityManager);
    }

    public static function getSubscribedEvents()
    {
        return [
            EventEnum::BEFORE_CREATE_ENTITY => 'onBefore',
            EventEnum::BEFORE_UPDATE_ENTITY => 'onBefore',
            EventEnum::BEFORE_DELETE_ENTITY => 'onBefore',
        ];
    }

    public function onBefore(EntityEvent $event)
    {
        throw new ReadOnlyException('Service readonly');
    }
}
