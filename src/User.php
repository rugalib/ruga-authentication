<?php

declare(strict_types=1);

namespace Ruga\Authentication;

use Ruga\Authentication\Container\UserFactory;
use Ruga\User\Role\RoleInterface;

/**
 * User.
 *
 * @see      UserFactory
 * @author   Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 */
final class User implements \Ruga\Authentication\UserInterface
{
    /** @var UserInterface */
    private $row;
    
    
    
    public function __construct(\Ruga\User\UserInterface $row)
    {
        $this->row = $row;
    }
    
    
    
    /**
     * Returns the database row of the user table
     *
     * @return \Ruga\User\UserInterface
     */
    public function getRow(): \Ruga\User\UserInterface
    {
        return $this->row;
    }
    
    
    
    /**
     * Get the unique user identity (id, username, email address or ...)
     */
    public function getIdentity(): string
    {
        return $this->getRow()->username;
    }
    
    
    
    /**
     * Get all user roles
     *
     * @return Iterable
     */
    public function getRoles(): iterable
    {
        return array_map(
            function (RoleInterface $role) {
                return $role->name;
            },
            iterator_to_array(
                $this->getRow()->findRoles()
            )
        );
    }
    
    
    
    /**
     * Get a detail $name if present, $default otherwise
     */
    public function getDetail(string $name, $default = null)
    {
        return $this->getDetails()[$name] ?? $default;
    }
    
    
    
    /**
     * Get all the details, if any
     */
    public function getDetails(): array
    {
        return $this->getRow()->toArray() ?? [];
    }
    
}
