<?php

namespace Core\Middlewares;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Server\MiddlewareInterface;

use Cake\Datasource\ConnectionManager;

/**
 * Middleware that is responsible for starting and committing a transaction
 */
class TransactionMiddleware implements MiddlewareInterface
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
        $connection = ConnectionManager::get('default');
        $connection->begin();

        $response = $handler->handle($request);

        if ($response->getStatusCode() > 399) { // Error
            $connection->rollback();
        } else {
            $connection->commit();
        }

        return $response;
    }
}
