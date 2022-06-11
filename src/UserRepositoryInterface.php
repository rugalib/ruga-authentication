<?php

declare(strict_types=1);

namespace Ruga\Authentication;

use Ruga\User\UserTableInterface;

/**
 * Interface to a user repository.
 *
 * @see      UserRepository
 * @author   Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 */
interface UserRepositoryInterface extends \Mezzio\Authentication\UserRepositoryInterface
{
    /**
     * Returns the user table used as user repository.
     *
     * @return UserTableInterface
     */
    public function getTable(): UserTableInterface;
}
