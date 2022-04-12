<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\Services;

use Kwai\Core\Domain\Exceptions\NotAllowedException;
use Kwai\Core\Domain\ValueObjects\UniqueId;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\Exceptions\UserInvitationNotFoundException;
use Kwai\Modules\Users\Domain\UserInvitationEntity;
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
     * @return UserInvitationEntity
     * @throws RepositoryException
     * @throws UserInvitationNotFoundException
     */
    public function getInvitation(UniqueId $uuid): UserInvitationEntity
    {
        return $this->repo->getByUniqueId($uuid);
    }

    /**
     * @param UserInvitationEntity $invitation
     * @return void
     * @throws NotAllowedException
     * @throws RepositoryException
     */
    public function remove(UserInvitationEntity $invitation)
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
