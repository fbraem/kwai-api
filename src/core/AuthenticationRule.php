<?php
/**
 * @package Kwai
 */
namespace Core;

use Tuupola\Middleware\JwtAuthentication\RuleInterface;
use Slim\Routing\RouteContext;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Implement the following rule:
 *   When argument 'auth' is set to true, authentication must be performed.
 */
class AuthenticationRule implements RuleInterface
{
    /**
     * Checks the current route for the argument 'auth'
     * @param  ServerRequestInterface $request
     * @return bool
     */
    public function __invoke(ServerRequestInterface $request)
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();

        return !empty($route) && $route->getArgument('auth', false);
    }
}
