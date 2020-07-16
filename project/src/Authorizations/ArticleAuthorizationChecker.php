<?php

declare(strict_types=1);

namespace App\Authorizations;

use App\Entity\Article;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class ArticleAuthorizationChecker
{
    const AUTHORIZED_METHODS = [
        Request::METHOD_POST,
        Request::METHOD_PUT,
        Request::METHOD_PATCH,
        Request::METHOD_DELETE
    ];

    private ?UserInterface $user;
    private AuthenticationChecker $authenticationChecker;

    public function __construct(Security $security, AuthenticationChecker $authenticationChecker)
    {
        $this->user = $security->getUser();
        $this->authenticationChecker = $authenticationChecker;
    }

    public function check(Article $article, string  $method): void
    {
        $this->authenticationChecker->isAuthenticated();

        if (!$this->checkMethod($method)) {
            $errorMsg = 'Method is not allowed';
            throw new UnauthorizedHttpException($errorMsg, $errorMsg);
        }

        if ($this->user->getId() !== $article->getAuthor()->getId()) {
            $errorMsg = 'You cannot perform any action. You are not the owner';
            throw new UnauthorizedHttpException($errorMsg, $errorMsg);
        }
    }

    public function checkMethod(string $method): bool
    {
        return in_array($method, self::AUTHORIZED_METHODS, true);
    }
}
