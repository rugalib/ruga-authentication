<?php

declare(strict_types=1);

namespace Ruga\Authentication\Container;

use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Mezzio\Handler\NotFoundHandler;
use Mezzio\Helper\UrlHelper;
use Ruga\Authentication\AuthenticationMiddleware;
use Ruga\Authentication\UserInterface;

class AuthenticationMiddlewareFactory
{
    
    
    public function __invoke(ContainerInterface $container): MiddlewareInterface
    {
        return new AuthenticationMiddleware(
            $container->get(UrlHelper::class),
            $container->get(NotFoundHandler::class),
            $container->get('config')['authentication'] ?? [],
            $container->get(UserInterface::class)
        );
    }
}
