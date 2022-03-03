<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\Services;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\Exceptions\NotAllowedException;
use Kwai\Core\Domain\ValueObjects\UniqueId;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\Exceptions\UserInvitationNotFoundException;
use Kwai\Modules\Users\Domain\UserInvitation;
use Kwai\Modules\Users\Repositories\UserInvitationRepository;

/**
 * Class UserInvitationService
 *
 * Service for a UserInvitation
 */
class UserInvitationService
{
    public function __construct(
        private UserInvitationRepository $repo
    ) {
    }

    /**
     * @param UniqueId $uuid
     * @return Entity<UserInvitation>
     * @throws RepositoryException
     * @throws UserInvitationNotFoundException
     */
    public function getInvitation(UniqueId $uuid)
    {
        return $this->repo->getByUniqueId($uuid);
    }

    /**
     * @param Entity<UserInvitation> $invitation
     * @return void
     * @throws NotAllowedException
     * @throws RepositoryException
     */
    public function remove(Entity $invitation)
    {
        if ($invitation->isConfirmed()) {
            throw new NotAllowedException(
                'User invitation',
                'Delete',
                'A confirmed user invitation cannot be deleted'
            );
        }
        $this->repo->remove($invitation);
    }
}
