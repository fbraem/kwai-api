<?php

namespace REST\Users\Actions;

use Psr\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Domain\User\UsersTable;
use Domain\User\UserTransformer;

use Core\Responses\ResourceResponse;

class BrowseAction
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        return (new ResourceResponse(
            UserTransformer::createForCollection(
                UsersTable::getTableFromRegistry()->find()->all()
            )
        ))($response);
    }
}
