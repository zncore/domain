<?php

namespace ZnCore\Domain\Behaviors;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use ZnCore\Base\Exceptions\ReadOnlyException;
use ZnCore\Domain\Enums\EventEnum;
use ZnCore\Domain\Events\EntityEvent;
use ZnCore\Domain\Interfaces\Libs\EntityManagerInterface;
use ZnCore\Domain\Traits\EntityManagerTrait;

class ReadOnlyBehavior implements EventSubscriberInterface
{

    use EntityManagerTrait;

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
