<?php
/**
 * @package Applications
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Applications\Users\Actions;

use Kwai\Applications\Users\Resources\UserInvitationResource;
use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Dependencies\DatabaseDependency;
use Kwai\Core\Infrastructure\Presentation\Action;
use Kwai\Core\Infrastructure\Presentation\Responses\JSONAPIResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\SimpleResponse;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\JSONAPI;
use Kwai\Modules\Users\Infrastructure\Repositories\UserInvitationDatabaseRepository;
use Kwai\Modules\Users\UseCases\BrowseUserInvitations;
use Kwai\Modules\Users\UseCases\BrowseUserInvitationsCommand;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\Annotation\Route;
use function depends;

/**
 * Class BrowseUsersAction
 *
 * Action to browse all user invitations
 */
#[Route(
    path: '/users/invitations',
    name: 'users.invitations.browse',
    options: ['auth' => true],
    methods: ['GET']
)]
class BrowseUserInvitationsAction extends Action
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
    public function __invoke(Request $request, Response $response, array $args)
    {
        $parameters = $request->getAttribute('parameters');

        $command = new BrowseUserInvitationsCommand();
        $command->limit = (int) ($parameters['page']['limit'] ?? 10);
        $command->offset = (int) ($parameters['page']['offset'] ?? 0);

        if (isset($parameters['filter']['active'])) {
            $command->active_time = $parameters['filter']['active'];
        }
        if (isset($parameters['filter']['active_timezone'])) {
            $command->active_timezone = $parameters['filter']['active_timezone'];
        }

        $repo = new UserInvitationDatabaseRepository($this->database);
        try {
            [$count, $invitations] = BrowseUserInvitations::create($repo)($command);
        } catch (RepositoryException $e) {
            $this->logException($e);
            return (
                new SimpleResponse(500, 'A repository exception occurred.')
            )($response);
        } catch (QueryException $e) {
            $this->logException($e);
            return (
                new SimpleResponse(500, 'A query exception occurred.')
            )($response);
        }

        $resources = $invitations->map(
            fn ($invitation) => new UserInvitationResource($invitation)
        );
        return (new JSONAPIResponse(
            JSONAPI\Document::createFromArray($resources->toArray())
                ->setMeta([
                    'count' => $count,
                    'limit' => $command->limit,
                    'offset' => $command->offset
                ])
        ))($response);
    }
}
