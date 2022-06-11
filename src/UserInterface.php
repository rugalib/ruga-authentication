<?php

declare(strict_types=1);

namespace Ruga\Authentication;

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
}
