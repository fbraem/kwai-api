<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\UseCases;

use Kwai\Core\Domain\Exceptions\NotAllowedException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\Exceptions\UserInvitationNotFoundException;
use Kwai\Modules\Users\Domain\UserInvitationEntity;
use Kwai\Modules\Users\Repositories\UserInvitationRepository;

/**
 * Class RenewUserInvitation
 *
 * Renews an invitation.
 */
class RenewUserInvitation
{
    private GetUserInvitation $getUserInvitation;

    public function __construct(
        private UserInvitationRepository $repo
    ) {
        $this->getUserInvitation = new GetUserInvitation($repo);
    }

    /**
     * @returns UserInvitationEntity
     * @throws RepositoryException
     * @throws UserInvitationNotFoundException
     * @throws NotAllowedException
     */
    public function execute(RenewUserInvitationCommand $command): UserInvitationEntity
    {
        $getCommand = new GetUserInvitationCommand();
        $getCommand->uuid = $command->uuid;
        $invitation = $this->getUserInvitation->execute($getCommand);

        $invitation->renew($command->expiration);
        $invitation->getTraceableTime()->markUpdated();
        $this->repo->update($invitation);

        return $invitation;
    }
}
