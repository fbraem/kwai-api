<?php

namespace REST\Users\Actions;

use Psr\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use PHPMailer\PHPMailer\Exception;

use Domain\User\UsersTable;
use Domain\User\UserTransformer;
use Domain\User\UserInvitationsTable;

use Respect\Validation\Validator as v;

use Kwai\Core\Infrastructure\Validators\ValidationException;
use Kwai\Core\Infrastructure\Validators\InputValidator;

use Kwai\Core\Infrastructure\Responses\ResourceResponse;
use Kwai\Core\Infrastructure\Responses\NotFoundResponse;
use Kwai\Core\Infrastructure\Responses\UnprocessableEntityResponse;

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
            return (new NotFoundResponse(_("Invitation doesn't exist.")))($response);
        }

        try {
            (new InputValidator([
                'data.attributes.email' => v::allOf(
                    v::email(),
                    v::callback(function ($value) {
                        // Check if the email address isn't used yet ...
                        $user = UsersTable::getTableFromRegistry()
                            ->find()
                            ->where(['email' => $value])
                            ->first()
                        ;
                        return $user == null;
                    })->setTemplate('{{name}} already in use')
                ),
                'data.attributes.password' => v::alnum()->notEmpty()->length(8, null),
                'data.attributes.first_name' => v::notEmpty()->length(1, 255),
                'data.attributes.last_name' => v::notEmpty()->length(1, 255)
            ]))->validate($data);

            $attributes = \JmesPath\search('data.attributes', $data);

            $usersTable = UsersTable::getTableFromRegistry();
            $user = $usersTable->newEntity();
            $user->email = $attributes['email'];
            $user->first_name = $attributes['first_name'];
            $user->last_name = $attributes['last_name'];
            $user->uuid = bin2hex(random_bytes(16));
            $user->password = password_hash($attributes['password'], PASSWORD_DEFAULT);

            $usersTable->save($user);
            $invitationsTable->delete($invitation);

            $response = (new ResourceResponse(
                UserTransformer::createForItem($user)
            ))($response)
                ->withStatus(201);
        } catch (ValidationException $ve) {
            $response = (new UnprocessableEntityResponse($ve->getErrors()))($response);
        }

        return $response;
    }
}
