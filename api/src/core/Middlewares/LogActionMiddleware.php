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

            $logsTable = \Domain\User\UserLogsTable::getTableFromRegistry();
            $log = $logsTable->newEntity();
            $log->user = $user;
            $log->action = $route->name;
            $log->rest = $route->extras['rest'] ?? '';
            $log->model_id = $route->attributes['id'] ?? 0;
            $logsTable->save($log);
        }
        return $delegate->process($request);
    }
}
