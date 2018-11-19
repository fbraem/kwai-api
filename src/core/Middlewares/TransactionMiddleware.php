<?php

namespace Core\Middlewares;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Cake\Datasource\ConnectionManager;

/**
 * Middleware that is responsible for starting and committing a transaction
 */
class TransactionMiddleware
{
    private $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $next)
    {
        $connection = ConnectionManager::get('default');
        $connection->begin();

        $response = $next($request, $response);

        if ($response->getStatusCode() > 399) { // Error
            $connection->rollback();
        } else {
            $connection->commit();
        }

        return $response;
    }
}
