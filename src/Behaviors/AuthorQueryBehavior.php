<?php

namespace ZnCore\Domain\Behaviors;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use ZnBundle\User\Domain\Interfaces\Services\AuthServiceInterface;
use ZnCore\Domain\Enums\EventEnum;
use ZnCore\Domain\Events\QueryEvent;
use ZnCore\Base\Libs\EntityManager\Interfaces\EntityManagerInterface;
use ZnCore\Base\Libs\EntityManager\Traits\EntityManagerAwareTrait;

class AuthorQueryBehavior implements EventSubscriberInterface
{

    use EntityManagerAwareTrait;

    private $authService;
    private $attributeName;

    public function __construct(EntityManagerInterface $entityManager, AuthServiceInterface $authService)
    {
        $this->setEntityManager($entityManager);
        $this->authService = $authService;
    }

    public function setAttributeName(string $attributeName): void
    {
        $this->attributeName = $attributeName;
    }

    public static function getSubscribedEvents()
    {
        return [
            EventEnum::BEFORE_FORGE_QUERY => 'onBeforeForgeQuery',
        ];
    }

    public function onBeforeForgeQuery(QueryEvent $event)
    {
        $query = $event->getQuery();
        $identityId = $this->authService->getIdentity()->getId();
        $query->where($this->attributeName, $identityId);
    }
}
