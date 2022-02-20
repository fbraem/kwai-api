<?php
/**
 * @package Applications
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Applications\Users\Actions;

use Kwai\Applications\Application;
use Kwai\Applications\Users\Resources\UserInvitationResource;
use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Dependencies\DatabaseDependency;
use Kwai\Core\Infrastructure\Presentation\Action;
use Kwai\Core\Infrastructure\Presentation\Responses\JSONAPIResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\NotFoundResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\SimpleResponse;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\JSONAPI;
use Kwai\Modules\Users\Domain\Exceptions\UserInvitationNotFoundException;
use Kwai\Modules\Users\Infrastructure\Repositories\UserInvitationDatabaseRepository;
use Kwai\Modules\Users\UseCases\GetUserInvitation;
use Kwai\Modules\Users\UseCases\GetUserInvitationCommand;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Symfony\Component\Routing\Annotation\Route;
use function depends;

/**
 * Class GetUserInvitationAction
 *
 * Action to get a user invitation with the given unique id.
 */
#[Route(
    path: '/users/invitations/{uuid}',
    name: 'users.invitations.get',
    requirements: ['uuid' => Application::UUID_REGEX],
    options: ['auth' => true],
    methods: ['GET']
)]
class GetUserInvitationAction extends Action
{
    public function __construct(
        private ?Connection $database = null
    ) {
        parent::__construct();
        $this->database ??= depends('kwai.database', DatabaseDependency::class);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $command = new GetUserInvitationCommand();
        $command->uuid = $args['uuid'];

        try {
            $invitation = GetUserInvitation::create(
                new UserInvitationDatabaseRepository($this->database)
            )($command);
        } catch (RepositoryException $e) {
            $this->logException($e);
            return (
                new SimpleResponse(500, 'A repository exception occurred.')
            )($response);
        } catch (UserInvitationNotFoundException) {
            return (new NotFoundResponse('User not found'))($response);
        }

        $resource = new UserInvitationResource($invitation);
        return (new JSONAPIResponse(
            JSONAPI\Document::createFromObject($resource)
        ))($response);
    }
}
