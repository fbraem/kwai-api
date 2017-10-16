<?php

namespace Core\Middlewares;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;

use Psr\Http\Message\ServerRequestInterface;

use Aura\Payload\Payload;

/**
 * Middleware that is responsible for executing the action
 */
class ActionMiddleware implements MiddlewareInterface
{
    public function process(
        ServerRequestInterface $request,
        DelegateInterface $delegate
    ) {
        $route = $request->getAttribute('clubman.route');
        $payload = new Payload();

        $json = json_decode((string) $request->getBody(), true);
        if (!$json['data']) {
            $json['data'] = [];
        }
        $payload->setInput($json);

        $actionClass = $route->handler;
        $action = new $actionClass();

        $responder = $action($request, $payload);
        return $responder->respond();
    }
}
