<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\UseCases;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\Exceptions\NotFoundException;
use Kwai\Core\Domain\UniqueId;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\User;
use Kwai\Modules\Users\Repositories\UserInvitationRepository;

/**
 * Class GetUserInvitation
 *
 * Use case to get a user invitation with the given unique id.
 */
class GetUserInvitation
{
    private UserInvitationRepository $repo;

    public function __construct(UserInvitationRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * Get a user invitation
     *
     * @param GetUserInvitationCommand $command
     * @return Entity<User>
     * @throws NotFoundException
     * @throws RepositoryException
     */
    public function __invoke(GetUserInvitationCommand $command): Entity
    {
        return $this->repo->getByUniqueId(new UniqueId($command->uuid));
    }
}
