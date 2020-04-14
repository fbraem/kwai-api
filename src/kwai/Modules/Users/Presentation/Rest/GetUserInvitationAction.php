<?php
declare(strict_types = 1);

namespace Kwai\Modules\Users\Presentation\Rest;

use Kwai\Core\Infrastructure\Presentation\Responses\NotFoundResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\ResourceResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\SimpleResponse;
use Kwai\Core\Domain\Exceptions\NotFoundException;
use Kwai\Core\Infrastructure\Presentation\Action;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Infrastructure\Repositories\UserInvitationDatabaseRepository;
use Kwai\Modules\Users\Presentation\Transformers\UserTransformer;
use Kwai\Modules\Users\UseCases\GetUserInvitation;
use Kwai\Modules\Users\UseCases\GetUserInvitationCommand;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class GetUserInvitationAction
 *
 * Action to get a user invitation with the given unique id.
 */
class GetUserInvitationAction extends Action
{
    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(Request $request, Response $response, $args)
    {
        $command = new GetUserInvitationCommand();
        $command->uuid = $args['uuid'];

        try {
            $database = $this->getContainerEntry('pdo_db');
            $user = (new GetUserInvitation(
                new UserInvitationDatabaseRepository($database)
            ))($command);
        } catch (NotFoundException $e) {
            return (new NotFoundResponse('User not found'))($response);
        } catch (RepositoryException $e) {
            return (
                new SimpleResponse(500, 'A repository exception occurred.')
            )($response);
        }

        return (new ResourceResponse(
            UserTransformer::createForItem(
                $user
            )
        ))($response);
    }
}
