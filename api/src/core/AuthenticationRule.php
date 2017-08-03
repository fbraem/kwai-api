<?php

namespace Core;

use Psr\Http\Message\RequestInterface;
use Slim\Middleware\JwtAuthentication\RuleInterface;

/**
 * Rule that force the execution of JWT authentication when
 * a login is required for a route
 */
class AuthenticationRule implements RuleInterface
{
    public function __invoke(RequestInterface $request)
    {
        $route = $request->getAttribute('clubman.route');
        return $route->auth && $route->auth['login'];
    }
}
