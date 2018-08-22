<?php

namespace REST\Users\Actions;

use Interop\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Domain\User\UsersTable;
use Domain\User\UserTransformer;

class BrowseAction extends \Core\Action
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        return $this->createJSONResponse(
            $response,
            UserTransformer::createForCollection(
                UsersTable::getTableFromRegistry()->find()->all()
            )
        );
    }
}
