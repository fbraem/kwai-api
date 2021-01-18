<?php
/**
 * @package Applications
 * @subpackage Admin
 */
declare(strict_types = 1);

namespace Kwai\Applications\User\Actions;

use Kwai\Core\Infrastructure\Presentation\Responses\NotFoundResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\ResourceResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\SimpleResponse;
use Kwai\Core\Infrastructure\Presentation\Action;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\Exceptions\UserInvitationNotFoundException;
use Kwai\Modules\Users\Infrastructure\Repositories\UserInvitationDatabaseRepository;
use Kwai\Modules\Users\Presentation\Transformers\UserInvitationTransformer;
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
            $user = GetUserInvitation::create(
                new UserInvitationDatabaseRepository($database)
            )($command);
        } catch (RepositoryException $e) {
            return (
                new SimpleResponse(500, 'A repository exception occurred.')
            )($response);
        } catch (UserInvitationNotFoundException $e) {
            return (new NotFoundResponse('User not found'))($response);
        }

        return (new ResourceResponse(
            UserInvitationTransformer::createForItem(
                $user
            )
        ))($response);
    }
}
