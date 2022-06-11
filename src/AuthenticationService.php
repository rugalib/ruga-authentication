<?php

declare(strict_types=1);

namespace Ruga\Authentication;

use Laminas\Authentication\Result;
use Mezzio\Session\SessionInterface;

/**
 * Class AuthenticationService
 *
 * @see     \Laminas\Authentication\AuthenticationService
 */
class AuthenticationService implements AuthenticationServiceInterface
{
    /** @var SessionInterface */
    private $session;
    
    /** @var \Closure */
    private $userFactory;
    
    
    
    public function __construct(SessionInterface $session, callable $userFactory)
    {
        $this->session = $session;
        
        $this->userFactory = function (string $identity, array $roles = [], array $details = []) use ($userFactory
        ): UserInterface {
            return $userFactory($identity, $roles, $details);
        };
    }
    
    
    
    /**
     * Authenticates and provides an authentication result
     *
     * @return Result
     */
    public function authenticate()
    {
    }
    
    
    
    /**
     * Returns true if and only if an identity is available
     *
     * @return bool
     */
    public function hasIdentity()
    {
        return $this->getIdentity() !== null;
    }
    
    
    
    /**
     * Returns the authenticated identity or null if no identity is available
     *
     * @return mixed|null
     */
    public function getIdentity()
    {
        foreach ([\Mezzio\Authentication\UserInterface::class, UserInterface::class] as $name) {
            if ($this->session->has($name)) {
                return ($this->userFactory)($this->session->get($name)['username']);
            }
        }
        return null;
    }
    
    
    
    public function getIdentityFromSession()
    {
        foreach ([\Mezzio\Authentication\UserInterface::class, UserInterface::class] as $name) {
            if ($this->session->has($name)) {
                return $this->session->get($name);
            }
        }
        return null;
    }
    
    
    
    /**
     * Clears the identity
     *
     * @return void
     */
    public function clearIdentity()
    {
        $this->session->unset(\Mezzio\Authentication\UserInterface::class);
        $this->session->unset(UserInterface::class);
    }
}