<?php

namespace Ruga\Authentication;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Authentication\UserInterface;
use Mezzio\Handler\NotFoundHandler;
use Mezzio\Helper\UrlHelper;
use Mezzio\Router\RouteResult;
use Mezzio\Session\SessionInterface;
use Mezzio\Session\SessionMiddleware;

class AuthenticationMiddleware implements MiddlewareInterface
{
    private const REDIRECT_ATTRIBUTE = 'authentication:redirect';
    
    /** @var UrlHelper */
    private $urlHelper;
    
    /** @var NotFoundHandler */
    private $notFoundHandler;
    
    /** @var array */
    private $config;
    
    /** @var \Closure */
    private $userFactory;
    
    
    
    public function __construct(
        UrlHelper $urlHelper,
        NotFoundHandler $notFoundHandler,
        array $config,
        callable $userFactory
    ) {
        $this->urlHelper = $urlHelper;
        $this->notFoundHandler = $notFoundHandler;
        $this->config = $config;
        
        $this->userFactory = function (string $identity, array $roles = [], array $details = []) use ($userFactory
        ): \Ruga\Authentication\UserInterface {
            return $userFactory($identity, $roles, $details);
        };
    }
    
    
    
    /**
     * Process an incoming server request.
     *
     * Processes an incoming server request in order to produce a response.
     * If unable to produce the response itself, it may delegate to the provided
     * request handler to do so.
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        \Ruga\Log::functionHead($this);
        
        // Get the session from the request
        /** @var SessionInterface $session */
        $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);
        
        // Check if user is already authenticated in session
        if ($session->has(UserInterface::class)) {
            /** @var \Ruga\Authentication\UserInterface $user */
            $user = ($this->userFactory)($session->get(UserInterface::class)['username'] ?? '');
            \Ruga\Log::addLog(
                "Session '{$session->getId()}' already logged in as user '{$user->getIdentity()}'",
                \Ruga\Log\Severity::INFORMATIONAL
            );
        } else {
            /** @var UserInterface $user */
            $user = ($this->userFactory)('GUEST');
            // Add the guest user to the session
            $session->set(
                UserInterface::class,
                [
                    'username' => $user->getIdentity(),
                    'roles' => $user->getRoles(),
                    'details' => $user->getDetails(),
                ]
            );
            \Ruga\Log::addLog(
                "Session '{$session->getId()}' logging in as user '{$user->getIdentity()}'",
                \Ruga\Log\Severity::INFORMATIONAL
            );
        }
        
        // Add the logged in user to the request
        $request = $request->withAttribute(UserInterface::class, $user);
        
        
        // 404 check
        // do not authenticate, if resource is not found
        $routeResult = $request->getAttribute(RouteResult::class);
        if ($routeResult->isFailure()) {
            return $this->notFoundHandler->handle($request);
        }
        
        
        // Only the users with role "guest" (defined in config) will be redirected to the login page
        if (in_array($this->config['redirect_role'], $user->getRoles())) {
            // Execute the request
            $response = $handler->handle($request);
            if (($request->getUri()->getPath() === $this->config['redirect'])
                || ($response->getStatusCode() !== StatusCodeInterface::STATUS_FORBIDDEN)) {
                // Return the response if the request was successful or the status was NOT forbidden (403)
                return $response;
            }
            \Ruga\Log::addLog(
                "Request to '{$request->getUri()->getPath()}' resulted in status {$response->getStatusCode()} {$response->getReasonPhrase()}"
            );
            
            // Store requested url in session for redirection after login
            $redirectAfterLogin = rtrim($this->urlHelper->getBasePath(), "/\\") . "/" . ltrim(
                    $this->getRedirectAfterLogin($request, $session),
                    "/\\"
                );
            $session->set(self::REDIRECT_ATTRIBUTE, $redirectAfterLogin);
            \Ruga\Log::addLog("Storing url '{$redirectAfterLogin}' for redirection after successful login");
            
            \Ruga\Log::addLog("Redirecting to login page '{$this->config['redirect']}'");
            return new RedirectResponse($this->urlHelper->generate($this->config['redirect']));
        }
        
        
        return $handler->handle($request);
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