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
use Kwai\Modules\Users\Domain\UserInvitation;
use Kwai\Modules\Users\Domain\UserInvitationEntity;
use Kwai\Modules\Users\Infrastructure\Repositories\UserInvitationDatabaseRepository;
use Kwai\Modules\Users\Repositories\UserInvitationRepository;

/**
 * Class UpdateUserInvitation
 */
class UpdateUserInvitation
{
    private GetUserInvitation $getUserInvitation;

    public function __construct(
        private UserInvitationRepository $repo
    ) {
        $this->getUserInvitation = new GetUserInvitation($repo);
    }

    public static function create(UserInvitationDatabaseRepository $repo): self
    {
        return new self($repo);
    }

    /**
     * @param UpdateUserInvitationCommand $command
     * @return UserInvitationEntity
     * @throws RepositoryException
     * @throws UserInvitationNotFoundException
     * @throws NotAllowedException
     */
    public function __invoke(UpdateUserInvitationCommand $command): UserInvitationEntity
    {
        $getUserCommand = new GetUserInvitationCommand();
        $getUserCommand->uuid = $command->uuid;
        $invitation = $this->getUserInvitation->execute($getUserCommand);

        if ($command->renew) {
            $invitation->renew();
        }

        $traceableTime = $invitation->getTraceableTime();
        $traceableTime->markUpdated();

        $invitation = new UserInvitationEntity(
            $invitation->id(),
            new UserInvitation(
                emailAddress: $invitation->getEmailAddress(),
                expiration: $invitation->getExpiration(),
                name: $invitation->getName(),
                creator: $invitation->getCreator(),
                remark: $command->remark ?? $invitation->getRemark(),
                uuid: $invitation->getUniqueId(),
                revoked: $invitation->isRevoked(),
                traceableTime: $traceableTime,
                confirmation: $invitation->getConfirmation()
            )
        );
        $this->repo->update($invitation);

        return $invitation;
    }
}
