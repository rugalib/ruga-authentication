<?php

declare(strict_types=1);

namespace Ruga\Authentication\Facade;

use Ruga\Authentication\AuthenticationServiceInterface;
use Ruga\Authentication\User;
use Ruga\Std\Facade\AbstractFacade;

/**
 * Auth facade
 *
 * @method static bool hasIdentity()
 * @method static User getIdentity()
 * @method static void clearIdentity()
 * @method static array getIdentityFromSession()
 *
 */
abstract class Auth extends AbstractFacade
{
    protected static function getFacadeInstanceName(): string
    {
        return AuthenticationServiceInterface::class;
    }
    
}