<?php

namespace REST\Users\Actions;

use Interop\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Domain\User\UsersTable;
use Domain\User\UserTransformer;

use Cake\Datasource\Exception\RecordNotFoundException;

use Core\Responses\ResourceResponse;
use Core\Responses\NotFoundResponse;

class ReadAction
{
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        try {
            $response = (new ResourceResponse(
                UserTransformer::createForItem(
                    UsersTable::getTableFromRegistry()->get($args['id'])
                )
            ))($response);
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $rnfe) {
            $response = (new NotFoundResponse(_("User doesn't exist.")))($response);
        }

        return $response;
    }
}
