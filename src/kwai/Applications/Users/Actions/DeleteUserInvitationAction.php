<?php
/**
 * @package Applications
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Applications\Users\Actions;

use Kwai\Applications\Application;
use Kwai\Core\Domain\Exceptions\NotAllowedException;
use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Dependencies\DatabaseDependency;
use Kwai\Core\Infrastructure\Presentation\Responses\NotFoundResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\SimpleResponse;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\Exceptions\UserInvitationNotFoundException;
use Kwai\Modules\Users\Infrastructure\Repositories\UserInvitationDatabaseRepository;
use Kwai\Modules\Users\UseCases\DeleteUserInvitation;
use Kwai\Modules\Users\UseCases\DeleteUserInvitationCommand;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DeleteUserInvitationAction
 *
 * Action for deleting a user invitation.
 */
#[Route(
    path: '/users/invitations/{uuid}',
    name: 'users.invitations.delete',
    requirements: ['uuid' => Application::UUID_REGEX],
    options: ['auth'=> true],
    methods: ['DELETE']
)]
class DeleteUserInvitationAction extends \Kwai\Core\Infrastructure\Presentation\Action
{
    public function __construct(
        private ?Connection $database = null,
        ?LoggerInterface $logger = null
    ) {
        parent::__construct($logger);
        $this->database ??= depends('kwai.database', DatabaseDependency::class);
    }

    /**
     * @inheritDoc
     */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $command = new DeleteUserInvitationCommand();
        $command->uuid = $args['uuid'];

        try {
            DeleteUserInvitation::create(new UserInvitationDatabaseRepository(
                $this->database
            ))($command);
        } catch (RepositoryException $e) {
            $this->logException($e);
            return (
                new SimpleResponse(500, 'A repository exception occurred.')
            )($response);
        } catch (UserInvitationNotFoundException $e) {
            return (new NotFoundResponse('User invitation not found'))($response);
        } catch (NotAllowedException $e) {
            return (
                new SimpleResponse(403, $e->getMessage())
            )($response);
        }
        return (
            new SimpleResponse(200, 'User invitation deleted')
        )($response);
    }
}
