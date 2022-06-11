<?php

declare(strict_types=1);

namespace Ruga\Authentication\Container;

use Mezzio\Authentication\UserInterface;
use Mezzio\Authentication\UserRepositoryInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Ruga\Authentication\Authentication;

class AuthenticationFactory
{
    public function __invoke(ContainerInterface $container) : Authentication
    {
        if (! $container->has(UserRepositoryInterface::class)) {
            throw new \Exception(
                'UserRepositoryInterface service is missing for authentication'
            );
        }
        
        $config = $container->get('config')['authentication'] ?? [];
        
        if (! isset($config['redirect'])) {
            throw new \Exception(
                'The redirect configuration is missing for authentication'
            );
        }
        
        if (! $container->has(UserInterface::class)) {
            throw new \Exception(
                'UserInterface factory service is missing for authentication'
            );
        }
        
        return new Authentication(
            $container->get(UserRepositoryInterface::class),
            $config,
            $container->get(ResponseInterface::class),
            $container->get(UserInterface::class)
        );
    }
    
}