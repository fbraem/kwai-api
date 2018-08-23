<?php

namespace REST\Users\Actions;

use Interop\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Domain\User\UsersTable;
use Domain\User\UserTransformer;

use Cake\Datasource\Exception\RecordNotFoundException;

class ReadAction
{
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        try {
            return (new \Core\ResourceResponse(
                UserTransformer::createForItem(
                    UsersTable::getTableFromRegistry()->get($args['id'])
                )
            ))($response);
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $rnfe) {
            return $response->withStatus(404, _("User doesn't exist."));
        }
    }
}
