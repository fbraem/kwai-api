<?php
/**
 * @package Applications
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Applications\Users\Actions;

use Kwai\Applications\Users\Resources\UserAccountResource;
use Kwai\Applications\Users\Schemas\UserInvitationConfirmationSchema;
use Kwai\Core\Domain\Exceptions\UnprocessableException;
use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Dependencies\DatabaseDependency;
use Kwai\Core\Infrastructure\Presentation\Action;
use Kwai\Core\Infrastructure\Presentation\InputSchemaProcessor;
use Kwai\Core\Infrastructure\Presentation\Responses\JSONAPIResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\NotFoundResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\SimpleResponse;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\JSONAPI;
use Kwai\Modules\Users\Domain\Exceptions\UserInvitationNotFoundException;
use Kwai\Modules\Users\Infrastructure\Repositories\UserAccountDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\UserInvitationDatabaseRepository;
use Kwai\Modules\Users\UseCases\ConfirmInvitation;
use Kwai\Modules\Users\UseCases\ConfirmInvitationCommand;
use Nette\Schema\Expect;
use Nette\Schema\Processor;
use Nette\Schema\ValidationException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\Annotation\Route;
use function depends;

/**
 * Class ConfirmInvitationAction
 *
 * Action to confirm an invitation.
 */
#[Route(
    path: '/users/invitations/{uuid}',
    name: 'users.invitations.confirm',
    methods: ['POST']
)]
class ConfirmInvitationAction extends Action
{
    public function __construct(
        private ?Connection $database = null,
        ?LoggerInterface $logger = null
    ) {
        parent::__construct($logger);
        $this->database ??= depends('kwai.database', DatabaseDependency::class);
    }

    /**
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     * @return Response
     */
    public function __invoke(
        Request $request,
        Response $response,
        array $args
    ): Response {
        try {
            $command = InputSchemaProcessor::create(new UserInvitationConfirmationSchema())
                ->process($request->getParsedBody())
            ;
        } catch (ValidationException $ve) {
            return (new SimpleResponse(422, $ve->getMessage()))($response);
        }

        if ($args['uuid'] !== $command->uuid) {
            return (new SimpleResponse(400, 'uuid in body and url should be the same.'))($response);
        }

        try {
            $userAccount = ConfirmInvitation::create(
                new UserInvitationDatabaseRepository($this->database),
                new UserAccountDatabaseRepository($this->database)
            )($command);
        } catch (UnprocessableException $e) {
            return (new SimpleResponse(422, $e->getMessage()))($response);
        } catch (UserInvitationNotFoundException) {
            return (new NotFoundResponse('User invitation does not exist'))($response);
        } catch (RepositoryException $e) {
            $this->logException($e);
            return (
                new SimpleResponse(500, 'A repository exception occurred.')
            )($response);
        }

        $resource = new UserAccountResource($userAccount);
        return (new JSONAPIResponse(
            JSONAPI\Document::createFromObject($resource)
        ))($response);
    }
}
