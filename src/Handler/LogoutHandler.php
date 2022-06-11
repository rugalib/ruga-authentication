<?php

declare(strict_types=1);

namespace Ruga\Authentication\Handler;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Mezzio\Session\SessionMiddleware;

/**
 *
 * @author   Roland Rusch, easy-smart solution GmbH <roland.rusch@easy-smart.ch>
 * @see      \Ruga\Authentication\Container\Handler\LogoutHandlerFactory
 *
 */
class LogoutHandler implements RequestHandlerInterface
{
    
    /** @var \Mezzio\Template\TemplateRendererInterface */
    private $renderer;
    
    /** @var \Mezzio\Helper\UrlHelper */
    private $urlHelper;
    
    
    
    public function __construct(
        \Mezzio\Template\TemplateRendererInterface $renderer,
        \Mezzio\Helper\UrlHelper $urlHelper
    ) {
        $this->renderer = $renderer;
        $this->urlHelper = $urlHelper;
    }
    
    
    
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        /** @var \Mezzio\Session\LazySession $session */
        $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);
        $session->clear();
        $session->regenerate();
        
        return new \Laminas\Diactoros\Response\RedirectResponse($this->urlHelper->generate('home'));
    }
}
