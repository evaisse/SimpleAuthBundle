<?php
/**
 * User: evaisse
 * Date: 13/01/2020
 * Time: 18:45
 */

namespace evaisse\SimpleAuthBundle\Security;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\InsufficientAuthenticationException;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

/**
 * Class EntryPoint
 * @package evaisse\SimpleAuthBundle\Security
 */
class EntryPoint implements AuthenticationEntryPointInterface
{

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Router
     */
    protected $router;

    /**
     * FormAuthenticationEntryPoint constructor.
     * @param LoggerInterface $logger
     * @param Router $router
     */
    public function __construct(LoggerInterface $logger, Router $router)
    {
        $this->logger = $logger;
        $this->router = $router;
    }

    /**
     * @param Request                      $request
     * @param AuthenticationException|null $authException
     * @return Response|void
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        $this->logger->debug("Building entrypoint url", ['uri' => $request->getRequestUri(), 'exception' => $authException, 'headers' => $request->headers->all(), 'x-requested-with' => $request->headers->get('X-REQUESTED-WITH', false)]);
        if ($request->isXmlHttpRequest()) {
            // In AJAX context, we send a special 401 response that will be handled by our scripts, redirects responses are not handled safely by ajax calls
            $this->logger->debug("Special redirect for ajax calls");
            $loginUrl = $this->router->generate('login', ['expire' => true]);
            return new Response('Unauthorized', 401, ['WWW-Authenticate' => "Redirect " . $loginUrl]);
        }

        $redirectParams = [];

        if ($authException instanceof AuthenticationException) {
            if (!($authException instanceof InsufficientAuthenticationException)) {
                $redirectParams['expire'] = true;
            } elseif ($request->query->has('ubiquite')) {
                $redirectParams['ubiquite'] = true;
            }
            if (in_array(
                    $request->attributes->get('_route'),
                    [
                        'login',
                        'login.logout',
                        'dashboard.home',
                    ],
                    true
                ) === false && $request->isMethod(Request::METHOD_GET)) {
                $redirectParams['org'] = $request->getRequestUri();
            }
        }

        $loginUrl = $this->router->generate('login', $redirectParams);
        $this->logger->debug('loginUrl : ' . $loginUrl, [
            '$authException' => get_class($authException),
            'redirectParams' => $redirectParams
        ]);
        return new RedirectResponse($loginUrl, 302);
    }


}