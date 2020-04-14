<?php

namespace REST\Users\Actions;

use Psr\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Domain\User\UserInvitationsTable;
use Domain\User\UserInvitationTransformer;

use Kwai\Core\Infrastructure\Presentation\Responses\ResourceResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\NotFoundResponse;

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
            $response = (new NotFoundResponse(_("Invitation doesn't exist.")))($response);
        } else {
            $response = (new ResourceResponse(
                UserInvitationTransformer::createForItem(
                    $invitation
                )
            ))($response);
        }

        return $response;
    }
}
