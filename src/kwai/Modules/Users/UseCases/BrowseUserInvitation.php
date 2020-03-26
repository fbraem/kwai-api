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
 * Class BrowseUserInvitation
 *
 * Use case to browse all users invitations.
 */
class BrowseUserInvitation
{
    private UserInvitationRepository $repo;

    public function __construct(UserInvitationRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * Browse all user invitations and returns a list
     *
     * @param BrowseUserInvitationCommand $command
     * @return Entity[]
     * @throws RepositoryException
     */
    public function __invoke(BrowseUserInvitationCommand $command): array
    {
        return $this->repo->getActive();
    }
}
