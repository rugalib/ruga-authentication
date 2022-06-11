<?php

declare(strict_types=1);

namespace Ruga\Authentication\Handler;

use Fig\Http\Message\RequestMethodInterface;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Mezzio\Session\SessionInterface;
use Mezzio\Authentication\UserInterface;
use Mezzio\Session\SessionMiddleware;
use Mezzio\Authentication\AuthenticationInterface;

/**
 *
 * @author   Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * @see      \Ruga\Authentication\Container\Handler\LoginHandlerFactory
 *
 */
class LoginHandler implements RequestHandlerInterface
{
    /** @var string Name of the attribute to store the redirect url */
    private const REDIRECT_ATTRIBUTE = 'authentication:redirect';
    
    /** @var TemplateRendererInterface */
    private $renderer;
    
    /** @var AuthenticationInterface */
    private $authAdapter;
    
    
    
    public function __construct(
        TemplateRendererInterface $renderer,
        AuthenticationInterface $authAdapter
    ) {
        $this->renderer = $renderer;
        $this->authAdapter = $authAdapter;
    }
    
    
    
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        \Ruga\Log::functionHead($this);
        
        $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);
        $redirectAfterLogin = $session->get(self::REDIRECT_ATTRIBUTE) ?? '/';
        \Ruga\Log::addLog('$redirectAfterLogin=' . $redirectAfterLogin);
        
        // Handle submitted credentials
        if ($request->getMethod() === RequestMethodInterface::METHOD_POST) {
            return $this->handleLoginAttempt($request, $session, $redirectAfterLogin);
        }
        
        
        // Display initial login form
        return new HtmlResponse(
            $this->renderer->render(
                'app::login',
                [
                    'error' => '',
                ]
            )
        );
    }
    
    
    
    private function handleLoginAttempt(
        ServerRequestInterface $request,
        SessionInterface $session,
        string $redirectAfterLogin
    ): ResponseInterface {
        // User session takes precedence over user/pass POST in
        // the auth adapter so we remove the session prior
        // to auth attempt
        $session->unset(UserInterface::class);
        
        // Login was successful
        if ($this->authAdapter->authenticate($request)) {
            $session->unset(self::REDIRECT_ATTRIBUTE);
            return new RedirectResponse($redirectAfterLogin);
        }
        
        \Ruga\Log::addLog("Credentials do not match", \Ruga\Log\Severity::WARNING, \Ruga\Log\Type::RESULT, $this);
        // Login failed
        return new HtmlResponse(
            $this->renderer->render(
                'app::login',
                [
                    'error' => 'Benutzername oder Passwort falsch.',
                ]
            )
        );
    }
}
