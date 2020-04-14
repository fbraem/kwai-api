<?php
/**
 * @package Kwai
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\Presentation\Rest;

use Kwai\Core\Infrastructure\Presentation\Responses\ResourceResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\SimpleResponse;
use Kwai\Core\Infrastructure\Presentation\Action;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Infrastructure\Repositories\UserInvitationDatabaseRepository;
use Kwai\Modules\Users\Presentation\Transformers\UserInvitationTransformer;
use Kwai\Modules\Users\UseCases\BrowseUserInvitations;
use Kwai\Modules\Users\UseCases\BrowseUserInvitationsCommand;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class BrowseUsersAction
 *
 * Action to browse all user invitations
 */
class BrowseUserInvitationsAction extends Action
{
    /**
     * @inheritDoc
     */
    public function __invoke(Request $request, Response $response, array $args)
    {
        $repo = new UserInvitationDatabaseRepository($this->getContainerEntry('pdo_db'));
        try {
            $invitations = (new BrowseUserInvitations($repo))(new BrowseUserInvitationsCommand());
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
