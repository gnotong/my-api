<?php

declare(strict_types=1);

namespace App\Authorizations;

use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class AuthenticationChecker
{
    private ?UserInterface $user;

    public function __construct(Security $security)
    {
        $this->user = $security->getUser();
    }

    public function isAuthenticated(): void
    {
        if (null !== $this->user) {
            return;
        }

        $errorMsg = 'You are not authenticated';
        throw new UnauthorizedHttpException($errorMsg, $errorMsg);
    }
}
