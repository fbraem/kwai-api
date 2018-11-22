<?php

namespace REST\Users\Actions;

use Interop\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Domain\User\UserInvitationsTable;
use Domain\User\UserInvitationTransformer;

use Core\Responses\ResourceResponse;

class BrowseInvitationAction
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $parameters = $request->getAttribute('parameters');

        $query = UserInvitationsTable::getTableFromRegistry()->find();
        if (isset($parameters['filter']['token'])) {
            $query->where(['token' => $parameters['filter']['token']]);
        }

        $count = $query->count();
        $query->limit($limit);
        $query->offset($offset);

        $invitations = $query->all();

        $limit = $parameters['page']['limit'] ?? 10;
        $offset = $parameters['page']['offset'] ?? 0;

        $resource = UserInvitationTransformer::createForCollection($invitations);
        $resource->setMeta([
            'limit' => intval($limit),
            'offset' => intval($offset),
            'count' => $count
        ]);

        return (new ResourceResponse($resource))($response);
    }
}
