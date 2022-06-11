<?php

declare(strict_types=1);

namespace Ruga\Authentication;

use Mezzio\Authentication\UserInterface;
use Ruga\User\UserTableInterface;

/**
 * UserRepository.
 *
 * @author   Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 */
final class UserRepository implements UserRepositoryInterface
{
    /** @var UserTableInterface */
    private $userTable;
    
    /** @var \Closure */
    private $userFactory;
    
    
    
    public function __construct(UserTableInterface $userTable, callable $userFactory)
    {
        $this->userTable = $userTable;
        
        $this->userFactory = function (string $identity, array $roles = [], array $details = []) use ($userFactory
        ): \Ruga\Authentication\UserInterface {
            return $userFactory($identity, $roles, $details);
        };
    }
    
    
    
    /**
     * Returns the user table used as user repository.
     *
     * @return UserTableInterface
     */
    public function getTable(): UserTableInterface
    {
        return $this->userTable;
    }
    
    
    
    /**
     * Authenticate the identity (id, username, email ...) using a password
     * or using only a credential string (e.g. token based credential)
     * It returns the authenticated user or null.
     *
     * @param string      $credential can be also a token
     *
     * @param string|null $password
     *
     * @return UserInterface|null
     * @throws \ReflectionException
     */
    public function authenticate(string $credential, string $password = null): ?UserInterface
    {
        /** @var \Ruga\User\UserInterface $user */
        if (!$user = $this->getTable()->findByIdentity($credential)->current()) {
            \Ruga\Log::addLog("User '{$credential}' failed to log in", \Ruga\Log\Severity::NOTICE);
            return null;
        }
        
        if ($user->verifyPassword($password)) {
            \Ruga\Log::addLog("User '{$user->idname}' logged in", \Ruga\Log\Severity::INFORMATIONAL);
            return ($this->userFactory)(
                $credential,
                $user->getRoles(),
                $user->toArray()
            );
        }
        
        \Ruga\Log::addLog("User '{$user->idname}' failed to log in", \Ruga\Log\Severity::NOTICE);
        return null;
    }
}
