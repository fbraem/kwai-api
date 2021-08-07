<?php

namespace Kwai\Core\Infrastructure\Middlewares;

use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Database\DatabaseException;
use Kwai\Core\Infrastructure\Dependencies\DatabaseDependency;
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
    public function __construct(
        private ?Connection $database = null)
    {
        $this->database ??= depends('kwai.database', DatabaseDependency::class);
    }

    public function process(
        Request $request,
        RequestHandler $handler
    ): ResponseInterface {
        try {
            $this->database->begin();
        } catch(DatabaseException)
        {
            //TODO: logging
        }

        $response = $handler->handle($request);

        if ($response->getStatusCode() > 399) { // Error
            try {
                $this->database->rollback();
            } catch(DatabaseException)
            {
                //TODO: logging
            }
        } else {
            try {
                $this->database->commit();
            } catch(DatabaseException)
            {
                //TODO: logging
            }
        }

        return $response;
    }
}
