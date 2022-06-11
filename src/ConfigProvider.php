<?php

declare(strict_types=1);

namespace Ruga\Authentication;

/**
 * ConfigProvider.
 *
 * @author   Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * @see      https://docs.mezzio.dev/mezzio/v3/features/container/config/
 */
class ConfigProvider
{
    public function __invoke()
    {
        return [
            'dependencies' => [
                'services' => [],
                'aliases' => [
                    \Mezzio\Authentication\UserInterface::class => \Ruga\Authentication\User::class,
                    \Ruga\Authentication\UserInterface::class => \Ruga\Authentication\User::class,
                    \Mezzio\Authentication\UserRepositoryInterface::class => \Ruga\Authentication\UserRepository::class,
                    \Ruga\Authentication\UserRepositoryInterface::class => \Ruga\Authentication\UserRepository::class,
                    \Mezzio\Authentication\AuthenticationInterface::class => \Mezzio\Authentication\Session\PhpSession::class,
                    \Ruga\Authentication\AuthenticationInterface::class => \Mezzio\Authentication\Session\PhpSession::class,
                    
                    \Laminas\Authentication\AuthenticationServiceInterface::class => \Ruga\Authentication\AuthenticationService::class,
                    \Ruga\Authentication\AuthenticationServiceInterface::class => \Ruga\Authentication\AuthenticationService::class,
                    
                    \Mezzio\Authorization\AuthorizationInterface::class => \Mezzio\Authorization\Rbac\LaminasRbac::class,
                ],
                'factories' => [
                    \Ruga\Authentication\User::class => \Ruga\Authentication\Container\UserFactory::class,
                    \Ruga\Authentication\UserRepository::class => \Ruga\Authentication\Container\UserRepositoryFactory::class,
                    \Ruga\Authentication\Authentication::class => \Ruga\Authentication\Container\AuthenticationFactory::class,
                    \Ruga\Authentication\AuthenticationService::class => \Ruga\Authentication\Container\AuthenticationServiceFactory::class,
                    
                    AuthenticationMiddleware::class => Container\AuthenticationMiddlewareFactory::class,
                    Handler\LoginHandler::class => Container\Handler\LoginHandlerFactory::class,
                    Handler\LogoutHandler::class => Container\Handler\LogoutHandlerFactory::class,
                ],
                'invokables' => [],
                'delegators' => [],
            ],
            
            'authentication' => [
                'username' => 'username',
                'password' => 'password',
                
                'redirect' => 'login',
                'redirect_role' => 'guest',
            ],
            
            'mezzio-authorization-rbac' => [
                /*
                'roles' => [
                    'system' => [],
                    'admin' => ['system'],
                    'user' => ['admin'],
                    'guest' => ['user'],
                ],
                'permissions' => [
                    'guest' => [
                        'home',
                        'login',
                        'logout',
                    ],
                    'admin' => [
                    ]
                ],
                */
            ],
        ];
    }
}
