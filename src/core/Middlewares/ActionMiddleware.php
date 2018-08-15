<?php

namespace Core\Middlewares;

use Interop\Http\Server\RequestHandlerInterface;
use Interop\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;

use Aura\Payload\Payload;

/**
 * Middleware that is responsible for executing the action
 */
class ActionMiddleware implements MiddlewareInterface
{
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $delegate
    ) {
        $route = $request->getAttribute('clubman.route');
        $payload = new Payload();

        $json = json_decode((string) $request->getBody(), true);
        if (!isset($json['data'])) {
            $json['data'] = [];
        }
        $payload->setInput($json);

        $actionClass = $route->handler;
        $action = new $actionClass();

        return $action($request, $payload);
    }
}
