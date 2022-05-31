<?php
/**
 * @package Applications
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Applications\Users\Actions;

use Kwai\Applications\Application;
use Kwai\Applications\Users\Security\InviterPolicy;
use Kwai\Core\Domain\Exceptions\NotAllowedException;
use Kwai\Core\Domain\ValueObjects\UniqueId;
use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Dependencies\DatabaseDependency;
use Kwai\Core\Infrastructure\Presentation\Responses\NotFoundResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\SimpleResponse;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\Exceptions\UserInvitationNotFoundException;
use Kwai\Modules\Users\Infrastructure\Repositories\UserInvitationDatabaseRepository;
use Kwai\Modules\Users\Services\UserInvitationService;
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
        $service = new UserInvitationService(
            new UserInvitationDatabaseRepository($this->database)
        );

        // 1. Check if the current user is allowed to delete the invitation.
        $uuid = new UniqueId($args['uuid']);
        try {
            $invitation = $service->getInvitation($uuid);
        } catch (RepositoryException $e) {
            $this->logException($e);
            return (
                new SimpleResponse(500, 'A repository exception occurred.')
            )($response);
        } catch (UserInvitationNotFoundException $e) {
            return (new NotFoundResponse('User invitation not found'))($response);
        }

        // 2. Check if remove is allowed
        $policy = new InviterPolicy(
            $request->getAttribute('kwai.user'),
            $invitation
        );
        if (!$policy->canRemove()) {
            return (
                new SimpleResponse(403, 'Not allowed to delete invitation')
            )($response);
        }

        // 3. Remove the invitation
        try {
            $service->remove($invitation);
        } catch (RepositoryException $e) {
            $this->logException($e);
            return (
                new SimpleResponse(500, 'A repository exception occurred.')
            )($response);
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
