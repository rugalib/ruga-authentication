<?php

declare(strict_types=1);

namespace Ruga\Authentication\Container\Handler;

use Mezzio\Helper\UrlHelper;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;
use Ruga\Authentication\Handler\LogoutHandler;

class LogoutHandlerFactory
{
    public function __invoke(ContainerInterface $container): LogoutHandler
    {
        return new LogoutHandler(
            $container->get(TemplateRendererInterface::class),
            $container->get(UrlHelper::class),
        );
    }
}
