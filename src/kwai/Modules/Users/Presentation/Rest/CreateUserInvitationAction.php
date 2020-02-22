<?php
declare(strict_types = 1);

namespace Kwai\Modules\Users\Presentation\Rest;

use Psr\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Core\Responses\ResourceResponse;

use Kwai\Modules\Users\Presentation\Transformers\UserInvitationTransformer;
use Kwai\Modules\Users\Infrastructure\Repositories\UserInvitationDatabaseRepository;
use Kwai\Modules\Users\UseCases\InviteUserCommand;
use Kwai\Modules\Users\UseCases\InviteUser;

class CreateUserInvitationAction
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $user = $request->getAttribute('kwai.user');
        $data = $request->getParsedBody();

        if (! isset($data['data']['attributes'])) {
            return (new SimpleResponse(422, 'Missing data'))($response);
        }

        try {
            $command = new InviteUserCommand($data['data']['attributes']);
        } catch (DataTransferObjectError $e) {
            return (new SimpleResponse(422, 'Missing data'))($response);
        }

        //TODO: for now, we pass the user as argument to the use case.
        //TODO: In the future, move this to an execution context?
        //TODO: Check if we need to create a InviteService?
        $invitation = (new InvitedUser(
            new UserInvitationDatabaseRepository(
                $this->container->get('pdo_db')
            ),
            $user
        ))($command);

        $command = new CreateEmailCommand([
            'to' => strval($invitation->getEmailAddress()),
            'uuid' => strval($invitation->getUniqueId()),
            'from' => '..',
            'body' => '',
            'remark' => ''
        ]);
        $mail = (new CreateEMail(
            new MailDatabaseRepository($this->container->get('pdo_db')),
            $user
        ))($command);

        return (new ResourceResponse(
            UserInvitationTransformer::createForItem(
                $invitation
            )
        ))($response);
    }
}
