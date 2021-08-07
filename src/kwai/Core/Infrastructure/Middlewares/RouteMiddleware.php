<?php
/**
 * @package Kwai
 * @subpackage Core
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Middlewares;

use Kwai\Core\Infrastructure\Presentation\Router;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class RouteMiddleware
 *
 * Middleware for handling routes with FastRoute
 */
class RouteMiddleware implements MiddlewareInterface
{
    public function __construct(
        private Router $router,
        private ResponseFactoryInterface $responseFactory
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        [$route, $action, $parameters, $extra] = $this->router->matchRequest($request);

        if (isset($action)) {
            $request = $request
                ->withAttribute('kwai.route', $route)
                ->withAttribute('kwai.action', $action)
                ->withAttribute('kwai.action.args', $parameters)
                ->withAttribute('kwai.extra', $extra)
            ;
        }
        return $handler->handle($request);
    }
}
