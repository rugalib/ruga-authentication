<?php

declare(strict_types=1);

namespace Ruga\Authentication\Test;


/**
 * @author Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 */
class UserRepositoryInterfaceTest extends \Ruga\Authentication\Test\PHPUnit\AbstractTestSetUp
{
    public function testCanGetUserRepository(): void
    {
        $container = $this->getContainer();
        /** @var \Ruga\Authentication\UserRepositoryInterface $userRepo */
        $userRepo = new \Ruga\Authentication\UserRepository(
            new \Ruga\User\UserTable($this->getAdapter()),
            function () {
            }
        );
        $this->assertInstanceOf(\Ruga\Authentication\UserRepository::class, $userRepo);
    }
    
    
    
    public function testCanGetUserRepositoryFromContainer(): void
    {
        $container = $this->getContainer();
        /** @var \Ruga\Authentication\UserRepositoryInterface $userRepo */
        $userRepo = $container->get(\Ruga\Authentication\UserRepositoryInterface::class);
        $this->assertInstanceOf(\Ruga\Authentication\UserRepository::class, $userRepo);
    }
    
    
    
    public function testCanAuthenticate(): void
    {
        $container = $this->getContainer();
        /** @var \Ruga\Authentication\UserRepositoryInterface $userRepo */
        $userRepo = $container->get(\Ruga\Authentication\UserRepositoryInterface::class);
        $this->assertInstanceOf(\Ruga\Authentication\UserRepository::class, $userRepo);
        
        $user = $userRepo->authenticate('admin', 'eZ.1234');
        $this->assertInstanceOf(\Ruga\Authentication\UserInterface::class, $user);
    }
    
    
    
    public function testCannotAuthenticateWrongPassword(): void
    {
        $container = $this->getContainer();
        /** @var \Ruga\Authentication\UserRepositoryInterface $userRepo */
        $userRepo = $container->get(\Ruga\Authentication\UserRepositoryInterface::class);
        $this->assertInstanceOf(\Ruga\Authentication\UserRepository::class, $userRepo);
        
        $user = $userRepo->authenticate('admin', 'wrongpassword');
        $this->assertNull($user);
    }
    
}
