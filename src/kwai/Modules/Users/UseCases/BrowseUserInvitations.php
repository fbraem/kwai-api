<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\UseCases;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Repositories\UserInvitationRepository;

/**
 * Class BrowseUserInvitations
 *
 * Use case to browse all users invitations.
 */
class BrowseUserInvitations
{
    private UserInvitationRepository $repo;

    public function __construct(UserInvitationRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * Browse all user invitations and returns a list
     *
     * @param BrowseUserInvitationsCommand $command
     * @return Entity[]
     * @throws RepositoryException
     */
    public function __invoke(BrowseUserInvitationsCommand $command): array
    {
        return $this->repo->getActive();
    }
}
