<?php

namespace REST\Users\Actions;

use Interop\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Domain\User\UserInvitationsTable;
use Domain\User\UserInvitationTransformer;

class ReadInvitationByTokenAction
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $parameters = $request->getAttribute('parameters');

        $invitation = UserInvitationsTable::getTableFromRegistry()
            ->find()
            ->where(['token' => $args['token']])
            ->first()
        ;
        if ($invitation == null) {
            return $response->withStatus(404, _("Invitation doesn't exist."));
        }

        return (new \Core\ResourceResponse(
            UserInvitationTransformer::createForItem(
            $invitation
        )
        ))($response);
    }
}
