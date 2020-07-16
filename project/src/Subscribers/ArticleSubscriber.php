<?php

declare(strict_types=1);

namespace App\Subscribers;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Authorizations\ArticleAuthorizationChecker;
use App\Entity\Article;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ArticleSubscriber implements EventSubscriberInterface
{
    private ArticleAuthorizationChecker $authorizationChecker;

    public function __construct(ArticleAuthorizationChecker $authorizationChecker)
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

        if (!$entity instanceof Article) {
            return;
        }

        $this->authorizationChecker->check($entity, $method);

        $entity->setUpdatedAt(new \DateTimeImmutable('now'));
    }
}
