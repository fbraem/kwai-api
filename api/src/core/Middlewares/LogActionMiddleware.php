<?php

namespace Core\Middlewares;

use Interop\Http\Server\RequestHandlerInterface;
use Interop\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;

use Aura\Payload\Payload;

/**
 * Middleware that is responsible for logging the action
 */
class LogActionMiddleware implements MiddlewareInterface
{
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $delegate
    ) {
        $user = $request->getAttribute('clubman.user');
        if ($user) {
            $route = $request->getAttribute('clubman.route');
            $db = $request->getAttribute('clubman.container')['db'];
            $log = new \Domain\User\UserLog($db, [
                'user' => $user,
                'action' => $route->name,
                'rest' => $route->extras['rest'] ?? '',
                'model_id' => $route->attributes['id'] ?? 0,
            ]);
            $log->store();
        }
        return $delegate->process($request);
    }
}
