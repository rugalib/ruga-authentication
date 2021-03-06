<?php

declare(strict_types=1);

namespace Ruga\Authentication\Test;


/**
 * @author Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 */
class UserInterfaceTest extends \Ruga\Authentication\Test\PHPUnit\AbstractTestSetUp
{
    public function testCanGetUser(): void
    {
        $container = $this->getContainer();
        /** @var \Ruga\Authentication\UserInterface $user */
        $user = ((new \Ruga\Authentication\Container\UserFactory())($container))('SYSTEM');
        $this->assertInstanceOf(\Ruga\Authentication\User::class, $user);
        echo $user->getIdentity();
    }
    
    
    
    public function testCanGetUserFromContainer(): void
    {
        $container = $this->getContainer();
        /** @var \Ruga\Authentication\UserInterface $user */
        $user = $container->get(\Ruga\Authentication\UserInterface::class)('admin');
        $this->assertInstanceOf(\Ruga\Authentication\User::class, $user);
        echo $user->getIdentity();
    }
    
    
    
    public function testCanGetUserRoles(): void
    {
        $container = $this->getContainer();
        /** @var \Ruga\Authentication\UserInterface $user */
        $user = $container->get(\Ruga\Authentication\UserInterface::class)('admin');
        $this->assertInstanceOf(\Ruga\Authentication\User::class, $user);
        echo $user->getIdentity() . PHP_EOL;
        print_r($user->getRoles());
    }
    
    
    
    public function testCanGetRoleConfig(): void
    {
        $container = $this->getContainer();
        $config = $container->get('config')['mezzio-authorization-rbac']['roles'];
        print_r($container->get('config')['mezzio-authorization-rbac']);
        $this->assertArrayHasKey('system', $config);
    }
    
}
