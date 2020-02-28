<?php
declare(strict_types = 1);

namespace Kwai\Modules\Users\Presentation\Rest;

use Kwai\Core\Domain\Exceptions\AlreadyExistException;
use Kwai\Core\Infrastructure\Template\MailTemplate;
use Kwai\Modules\Mails\Infrastructure\Repositories\MailDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\UserDatabaseRepository;
use Psr\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Spatie\DataTransferObject\DataTransferObjectError;

use Core\Responses\ResourceResponse;
use Core\Responses\SimpleResponse;

use Kwai\Modules\Users\Presentation\Transformers\UserInvitationTransformer;
use Kwai\Modules\Users\Infrastructure\Repositories\UserInvitationDatabaseRepository;
use Kwai\Modules\Users\UseCases\InviteUserCommand;
use Kwai\Modules\Users\UseCases\InviteUser;

class CreateUserInvitationAction
{
    private ContainerInterface $container;

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
        try {
            $invitation = (new InviteUser(
                new UserInvitationDatabaseRepository(
                    $this->container->get('pdo_db')
                ),
                new UserDatabaseRepository(
                    $this->container->get('pdo_db')
                ),
                new MailDatabaseRepository(
                    $this->container->get('pdo_db')
                ),
                new MailTemplate(
                    'User Invitation',
                    $this->container->get('template')->createTemplate(''),
                    $this->container->get('template')->createTemplate(''),
                ),
                $user
            ))($command);
        } catch (AlreadyExistException $e) {
            return (new SimpleResponse(
                422,
                'The email address is already used'
            ))($response);
        }

        return (new ResourceResponse(
            UserInvitationTransformer::createForItem(
                $invitation
            )
        ))($response);
    }
}
