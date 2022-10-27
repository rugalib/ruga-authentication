<?php

declare(strict_types=1);

namespace Ruga\Authentication\Container\Handler;

use Mezzio\Helper\UrlHelper;
use Psr\Container\ContainerInterface;
use Mezzio\Template\TemplateRendererInterface;
use Mezzio\Authentication\AuthenticationInterface;
use Ruga\Authentication\Handler\LoginHandler;

class LoginHandlerFactory
{
    public function __invoke(ContainerInterface $container): LoginHandler
    {
        return new LoginHandler(
            $container->get(TemplateRendererInterface::class),
            $container->get(AuthenticationInterface::class),
            $container->get(UrlHelper::class),
        );
    }
}
