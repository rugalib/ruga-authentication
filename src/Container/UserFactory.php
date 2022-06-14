<?php

declare(strict_types=1);

namespace Ruga\Authentication\Container;

use Psr\Container\ContainerInterface;
use Ruga\Authentication\User;
use Ruga\Authentication\UserInterface;
use Ruga\Db\Adapter\Adapter;
use Ruga\User\UserTableInterface;

/**
 * Produces a callable factory capable of itself producing a UserInterface
 * instance; this approach is used to allow substituting alternative user
 * implementations without requiring extensions to existing repositories.
 */
class UserFactory
{
    public function __invoke(ContainerInterface $container, string $resolvedName="", ?array $options=[]): callable
    {
        /** @var UserTableInterface $userTable */
//        $userTable=$container->get(UserTableInterface::class);
        // Using Adapter here to not create an infinite loop when instantiating a User table
        /** @var Adapter $adapter */
        $adapter=$container->get(Adapter::class);
        
        return function (string $identity, array $roles = [], array $details = []) use ($adapter): UserInterface {
            $userTable=$adapter->tableFactory(UserTableInterface::class);
            if(!$user=$userTable->findByIdentity($identity)->current()) {
                $user=$userTable->findByIdentity('GUEST')->current();
            }
            
            return new User($user);
        };
    }
}
