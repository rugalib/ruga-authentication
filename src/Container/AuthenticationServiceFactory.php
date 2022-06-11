<?php

declare(strict_types=1);

namespace Ruga\Authentication\Container;

use Mezzio\Session\Session;
use Psr\Container\ContainerInterface;
use Ruga\Authentication\AuthenticationService;
use Ruga\Authentication\AuthenticationServiceInterface;
use Ruga\Authentication\UserInterface;

class AuthenticationServiceFactory
{
    public function __invoke(ContainerInterface $container, string $resolvedName, ?array $options): AuthenticationServiceInterface
    {
        $session=new Session($_SESSION ?? []);
        $userFactory=$container->get(UserInterface::class);
        
        return new AuthenticationService(
            $session,
            $userFactory
        );
    }
    
}