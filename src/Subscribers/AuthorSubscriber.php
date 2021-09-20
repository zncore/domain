<?php

namespace ZnCore\Domain\Subscribers;

use App\News\Domain\Entities\CommentEntity;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use ZnBundle\User\Domain\Exceptions\UnauthorizedException;
use ZnBundle\User\Domain\Interfaces\Services\AuthServiceInterface;
use ZnCore\Domain\Enums\EventEnum;
use ZnCore\Domain\Events\EntityEvent;
use ZnCore\Domain\Helpers\EntityHelper;

class AuthorSubscriber implements EventSubscriberInterface
{

    private $authService;
    private $attribute;

    public function __construct(
        AuthServiceInterface $authService
    )
    {
        $this->authService = $authService;
    }

    public function setAttribute(string $attribute): void
    {
        $this->attribute = $attribute;
    }

    public static function getSubscribedEvents()
    {
        return [
            EventEnum::BEFORE_CREATE_ENTITY => 'onCreateComment'
        ];
    }

    public function onCreateComment(EntityEvent $event)
    {
        /** @var CommentEntity $entity */
        $entity = $event->getEntity();
        $identityId = $this->authService->getIdentity()->getId();
        EntityHelper::setAttribute($entity, $this->attribute, $identityId);
        try {

        } catch (UnauthorizedException $e) {
        }
    }
}
