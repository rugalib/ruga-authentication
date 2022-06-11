<?php

declare(strict_types=1);

namespace Ruga\Authentication;

use Mezzio\Authentication\AuthenticationInterface;
use Mezzio\Authentication\UserInterface;
use Mezzio\Authentication\UserRepositoryInterface;
use Mezzio\Session\Session;
use Mezzio\Session\SessionInterface;
use Mezzio\Session\SessionMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Authentication implements AuthenticationInterface
{
    private const REDIRECT_AFTER_LOGIN_ATTRIBUTE = 'authentication:redirect_after_login';
    
    /** @var UserRepositoryInterface */
    private $repository;
    
    /** @var array */
    private $config;
    
    /** @var callable */
    private $responseFactory;
    
    /** @var callable */
    private $userFactory;
    
    
    
    public function __construct(
        UserRepositoryInterface $repository,
        array $config,
        callable $responseFactory,
        callable $userFactory
    ) {
        $this->repository = $repository;
        $this->config = $config;
        
        // Ensures type safety of the composed factory
        $this->responseFactory = function () use ($responseFactory): ResponseInterface {
            return $responseFactory();
        };
        
        // Ensures type safety of the composed factory
        $this->userFactory = function (
            string $identity,
            array $roles = [],
            array $details = []
        ) use ($userFactory): UserInterface {
            return $userFactory($identity, $roles, $details);
        };
    }
    
    
    
    /**
     * Authenticate the PSR-7 request and return a valid user
     * or null if not authenticated
     */
    public function authenticate(ServerRequestInterface $request): ?UserInterface
    {
        \Ruga\Log::functionHead();
        
        /** @var Session $session */
        if (!$session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE)) {
            throw new Exception\NoSessionException("Session not found. Is SessionMiddleware defined in the pipeline?");
        }
        
        return ($this->userFactory)('');
    }
    
    
    
    /**
     * Generate the unauthorized response
     */
    public function unauthorizedResponse(ServerRequestInterface $request): ResponseInterface
    {
        \Ruga\Log::functionHead();
        
        /** @var Session $session */
        if (!$session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE)) {
            throw new Exception\NoSessionException("Session not found. Is SessionMiddleware defined in the pipeline?");
        }
        $redirectAfterLogin = ltrim($this->getRedirectAfterLogin($request, $session), "/\\");
        
        $session->set(
            self::REDIRECT_AFTER_LOGIN_ATTRIBUTE,
            $redirectAfterLogin
        );
        
        \Ruga\Log::addLog("Redirecting to login page '{$this->config['redirect']}'", \Ruga\Log\Severity::NOTICE);
        \Ruga\Log::addLog("Redirecting after login to '{$redirectAfterLogin}'", \Ruga\Log\Severity::INFORMATIONAL);
        
        return ($this->responseFactory)()
            ->withHeader(
                'Location',
                $this->config['redirect']
            )
            ->withStatus(302);
    }
    
    
    
    private function getRedirectAfterLogin(ServerRequestInterface $request, SessionInterface $session): string
    {
        $redirectAfterLogin = $request->getUri()->getPath();
        if (in_array(
            $redirectAfterLogin,
            [
                '',
                $this->config['redirect'],
            ],
            true
        )) {
            $redirectAfterLogin = '/';
        }
        
        return $redirectAfterLogin;
    }
    
    
}