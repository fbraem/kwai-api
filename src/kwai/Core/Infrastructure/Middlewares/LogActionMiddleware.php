<?php

namespace Kwai\Core\Infrastructure\Middlewares;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Server\MiddlewareInterface;

/**
 * Middleware that is responsible for logging the action
 */
class LogActionMiddleware implements MiddlewareInterface
{
    public function process(
        Request $request,
        RequestHandler $handler
    ): ResponseInterface {
        $response = $handler->handle($request);

        //TODO: Remove CakePHP dependency, for now, we disable this...
/*
        $route = $request->getAttribute('route');
        if (! empty($route)) {
            $user = $request->getAttribute('kwai.user');
            if ($user) {
                $logsTable = UserLogsTable::getTableFromRegistry();
                $log = $logsTable->newEntity();
                $log->user = $user;
                $log->action = $route->getName();
                $log->model_id = $route->getArguments()['id'] ?? 0;
                $log->status = $response->getStatusCode();
                $logsTable->save($log);
            }
        }
*/
        return $response;
    }
}
