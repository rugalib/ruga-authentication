<?php

declare(strict_types=1);

namespace Ruga\Authentication\Container;

use Psr\Container\ContainerInterface;
use Ruga\Authentication\UserRepository;
use Ruga\Authentication\UserRepositoryInterface;
use Ruga\User\UserTableInterface;

/**
 * Produces a callable factory capable of itself producing a UserInterface
 * instance; this approach is used to allow substituting alternative user
 * implementations without requiring extensions to existing repositories.
 */
class UserRepositoryFactory
{
    public function __invoke(ContainerInterface $container, string $resolvedName, ?array $options): UserRepositoryInterface
    {
        /** @var UserTableInterface $userTable */
        $userTable=$container->get(UserTableInterface::class);
        $userFactory=$container->get(\Ruga\Authentication\UserInterface::class);
        return new UserRepository($userTable, $userFactory);
    }
}
