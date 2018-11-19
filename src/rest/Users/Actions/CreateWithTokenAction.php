<?php

namespace REST\Users\Actions;

use Interop\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Cake\Datasource\Exception\RecordNotFoundException;

use PHPMailer\PHPMailer\Exception;

use Domain\User\UsersTable;
use Domain\User\UserTransformer;
use Domain\User\UserInvitationsTable;

class CreateWithTokenAction
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $data = $request->getParsedBody();

        $invitationsTable = UserInvitationsTable::getTableFromRegistry();
        $invitation = $invitationsTable
            ->find()
            ->where(['token' => $args['token']])
            ->first()
        ;
        if ($invitation == null) {
            return $response->withStatus(404, _("Invitation doesn't exist."));
        }

        $attributes = \JmesPath\search('data.attributes', $data);

        $usersTable = UsersTable::getTableFromRegistry();

        // Check if the email address isn't used yet ...
        $user = $usersTable
            ->find()
            ->where(['email' => $attributes['email']])
            ->first()
        ;
        if ($user != null) {
            // This is not ok!
            $response
                ->getBody()
                ->write(
                    json_encode([
                        'errors' => [
                            [
                                'source' => [
                                    'pointer' => '/data/attributes/email'
                                ],
                                'title' => _('Email address already in use')
                                ]
                            ]
                    ])
                )
            ;
            return $response
                ->withStatus(422)
                ->withHeader('content-type', 'application/vnd.api+json')
            ;
        }

        $user = $usersTable->newEntity();
        $user->email = $attributes['email'];
        $user->first_name = $attributes['first_name'];
        $user->last_name = $attributes['last_name'];
        $user->password = password_hash($attributes['password'], PASSWORD_DEFAULT);

        $usersTable->save($user);
        $invitationsTable->delete($invitation);

        return \Core\ResourceResponse::respond(
            UserTransformer::createForItem($user),
            $response
        )->withStatus(201);
    }
}
