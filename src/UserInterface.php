<?php

declare(strict_types=1);

namespace Ruga\Authentication;

use Ruga\User\Role\RoleInterface;

/**
 * Interface to a template.
 *
 * @see      User
 * @author   Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 */
interface UserInterface extends \Mezzio\Authentication\UserInterface
{
    /**
     * Returns the database row of the user table
     *
     * @return \Ruga\User\UserInterface
     */
    public function getRow(): \Ruga\User\UserInterface;
    
    
    
    /**
     * Check if the user has the given role.
     *
     * @param string|RoleInterface $role
     *
     * @return bool
     */
    public function hasRole($role): bool;
}
