<?php
/**
 * @package Kwai
 * @subpackage Core
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Middlewares;

use Exception;
use Kwai\Core\Infrastructure\Presentation\RouteException;
use Kwai\Core\Infrastructure\Presentation\Router;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class RouteMiddleware
 *
 * Middleware for handling routes with FastRoute
 */
class RouteMiddleware implements MiddlewareInterface
{
    public function __construct(
        private Router|RouteCollection $router
    ) {
    }

    /**
     * @throws RouteException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($this->router instanceof Router) {
            [$route, $action, $parameters, $extra] = $this->router->matchRequest($request);

            if (isset($action)) {
                $request = $request
                    ->withAttribute('kwai.route', $route)
                    ->withAttribute('kwai.action', $action)
                    ->withAttribute('kwai.action.args', $parameters)
                    ->withAttribute('kwai.extra', $extra);
            }
        } else {
            $symfonyRequest = (new HttpFoundationFactory())->createRequest($request);
            $context = new RequestContext();
            $context->fromRequest($symfonyRequest);

            $matcher = new UrlMatcher($this->router, $context);
            try {
                $parameters = $matcher->matchRequest($symfonyRequest);
                $route = $parameters['_route'] ?? null;
                unset($parameters['_route']);
                $action = $parameters['_action'] ?? null;
                unset($parameters['_action']);
                $extra = $parameters['_extra'] ?? [];
                unset($parameters['_extra']);
                $request = $request
                    ->withAttribute('kwai.route', $route)
                    ->withAttribute('kwai.action', $action)
                    ->withAttribute('kwai.action.args', $parameters)
                    ->withAttribute('kwai.extra', $extra);
            } catch (Exception $e) {
                throw new RouteException(message: 'Could not find a route', previous: $e);
            }
        }
        return $handler->handle($request);
    }
}
