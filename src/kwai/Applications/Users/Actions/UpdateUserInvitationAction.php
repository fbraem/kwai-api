<?php
/**
 * @package Applications
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Applications\Users\Actions;

use Kwai\Applications\Application;
use Kwai\Applications\Users\Resources\UserInvitationResource;
use Kwai\Applications\Users\Schemas\UserInvitationSchema;
use Kwai\Core\Domain\Exceptions\NotAllowedException;
use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Dependencies\DatabaseDependency;
use Kwai\Core\Infrastructure\Presentation\Action;
use Kwai\Core\Infrastructure\Presentation\InputSchemaProcessor;
use Kwai\Core\Infrastructure\Presentation\Responses\ForbiddenResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\JSONAPIResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\NotFoundResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\SimpleResponse;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\Exceptions\UserInvitationNotFoundException;
use Kwai\Modules\Users\Infrastructure\Repositories\UserInvitationDatabaseRepository;
use Kwai\Modules\Users\UseCases\UpdateUserInvitation;
use Kwai\JSONAPI;
use Nette\Schema\ValidationException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UpdateUserInvitationAction
 */
#[Route(
    path: '/users/invitations/{uuid}',
    name: 'users.invitations.update',
    requirements: ['uuid' => Application::UUID_REGEX],
    options: ['auth' => true],
    methods: ['PATCH']
)]
class UpdateUserInvitationAction extends Action
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
        try {
            $command = InputSchemaProcessor::create(new UserInvitationSchema())
                ->process($request->getParsedBody())
            ;
        } catch (ValidationException $ve) {
            return (new SimpleResponse(422, $ve->getMessage()))($response);
        }

        if ($command->uuid != $args['uuid']) {
            return (new SimpleResponse(400, 'id in body and url should be the same.'))($response);
        }

        try {
            $invitation = UpdateUserInvitation::create(
                new UserInvitationDatabaseRepository($this->database)
            )($command);
        } catch (NotAllowedException $e) {
            return (
                new ForbiddenResponse($e->getMessage())
            )($response);
        } catch (RepositoryException $e) {
            return (
                new SimpleResponse(500, 'A repository exception occurred.')
            )($response);
        } catch (UserInvitationNotFoundException $e) {
            return (new NotFoundResponse((string) $e))($response);
        }

        $resource = new UserInvitationResource($invitation);
        return (new JSONAPIResponse(
            JSONAPI\Document::createFromObject($resource)
        ))($response);
    }
}
