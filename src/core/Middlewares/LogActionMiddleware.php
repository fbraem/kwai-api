<?php

namespace Core\Middlewares;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Domain\User\UserLogsTable;

use Cake\Datasource\Exception\RecordNotFoundException;

use League\OAuth2\Server\Exception\OAuthServerException;

/**
 * Middleware that is responsible for logging the action
 */
class LogActionMiddleware
{
    private $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $next)
    {
        $response = $next($request, $response);

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
