<?php

declare(strict_types=1);

namespace App\Subscribers;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Authorizations\UserAuthorizationChecker;
use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class UserSubscriber implements EventSubscriberInterface
{
    private UserAuthorizationChecker $authorizationChecker;

    public function __construct(UserAuthorizationChecker $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['check', EventPriorities::PRE_VALIDATE]
        ];
    }

    public function check(ViewEvent $event): void
    {
        $entity = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if(!$entity instanceof User) {
            return;
        }

        $this->authorizationChecker->check($entity, $method);
    }
}
