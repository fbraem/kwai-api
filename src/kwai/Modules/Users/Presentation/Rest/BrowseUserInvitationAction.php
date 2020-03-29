<?php
/**
 * @package Kwai
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\Presentation\Rest;

use Core\Responses\ResourceResponse;
use Core\Responses\SimpleResponse;
use Kwai\Core\Infrastructure\Presentation\Action;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Infrastructure\Repositories\UserInvitationDatabaseRepository;
use Kwai\Modules\Users\Presentation\Transformers\UserInvitationTransformer;
use Kwai\Modules\Users\UseCases\BrowseUserInvitation;
use Kwai\Modules\Users\UseCases\BrowseUserInvitationCommand;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class BrowseUserAction
 *
 * Action to browse all user invitations
 */
class BrowseUserInvitationAction extends Action
{
    /**
     * @inheritDoc
     */
    public function __invoke(Request $request, Response $response, array $args)
    {
        $repo = new UserInvitationDatabaseRepository($this->getContainerEntry('pdo_db'));
        try {
            $invitations = (new BrowseUserInvitation($repo))(new BrowseUserInvitationCommand());
            return (new ResourceResponse(
                UserInvitationTransformer::createForCollection($invitations)
            ))($response);
        } catch (RepositoryException $e) {
            return (
                new SimpleResponse(500, 'A repository exception occurred.')
            )($response);
        }
    }
}
