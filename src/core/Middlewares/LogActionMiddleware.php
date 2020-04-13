<?php

namespace Kwai\Core\Infrastructure\Middlewares;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Server\MiddlewareInterface;

use Domain\User\UserLogsTable;

use Cake\Datasource\Exception\RecordNotFoundException;

use League\OAuth2\Server\Exception\OAuthServerException;

/**
 * Middleware that is responsible for logging the action
 */
class LogActionMiddleware implements MiddlewareInterface
{
    private $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function process(
        Request $request,
        RequestHandler $handler
    ): ResponseInterface {
        $response = $handler->handle($request);

        $route = $request->getAttribute('route');
        if (! empty($route)) {
            $user = $request->getAttribute('clubman.user');
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

        return $response;
    }
}
