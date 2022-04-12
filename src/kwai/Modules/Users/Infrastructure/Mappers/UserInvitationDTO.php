<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Infrastructure\Mappers;

use Kwai\Core\Domain\ValueObjects\Creator;
use Kwai\Core\Domain\ValueObjects\EmailAddress;
use Kwai\Core\Domain\ValueObjects\LocalTimestamp;
use Kwai\Core\Domain\ValueObjects\Name;
use Kwai\Core\Domain\ValueObjects\Timestamp;
use Kwai\Core\Domain\ValueObjects\TraceableTime;
use Kwai\Core\Domain\ValueObjects\UniqueId;
use Kwai\Modules\Users\Domain\UserInvitation;
use Kwai\Modules\Users\Domain\UserInvitationEntity;
use Kwai\Modules\Users\Infrastructure\UserInvitationsTable;
use Kwai\Modules\Users\Infrastructure\UsersTable;

/**
 * Class UserInvitationDTO
 */
final class UserInvitationDTO
{
    public function __construct(
        public UserInvitationsTable $userInvitation = new UserInvitationsTable(),
        public UsersTable $user = new UsersTable()
    ) {
    }

    /**
     * Create a UserInvitation domain object from a database row
     *
     * @return UserInvitation
     */
    public function create(): UserInvitation
    {
        return new UserInvitation(
            emailAddress: new EmailAddress($this->userInvitation->email),
            expiration: new LocalTimestamp(
                Timestamp::createFromString($this->userInvitation->expired_at),
                $this->userInvitation->expired_at_timezone
            ),
            name: $this->userInvitation->name,
            creator: new Creator(
                $this->user->id,
                new Name(
                    $this->user->first_name,
                    $this->user->last_name
                )
            ),
            remark: $this->userInvitation->remark,
            uuid: new UniqueId($this->userInvitation->uuid),
            revoked: $this->userInvitation->revoked === 1,
            traceableTime: new TraceableTime(
                Timestamp::createFromString($this->userInvitation->created_at),
                $this->userInvitation->updated_at
                    ? Timestamp::createFromString($this->userInvitation->updated_at)
                    : null
            ),
            confirmation: $this->userInvitation->confirmed_at
                ? Timestamp::createFromString($this->userInvitation->confirmed_at)
                : null
        );
    }

    /**
     * Create a UserInvitation entity
     *
     * @return UserInvitationEntity
     */
    public function createEntity(): UserInvitationEntity
    {
        return new UserInvitationEntity(
            $this->userInvitation->id,
            $this->create()
        );
    }

    /**
     * Persist the UserInvitation domain object to a database row
     *
     * @param UserInvitation $invitation
     * @return $this
     */
    public function persist(UserInvitation $invitation): static
    {
        $this->userInvitation->uuid = (string) $invitation->getUniqueId();
        $this->userInvitation->email = (string) $invitation->getEmailAddress();
        $this->userInvitation->expired_at = (string) $invitation->getExpiration()->getTimestamp();
        $this->userInvitation->expired_at_timezone = $invitation->getExpiration()->getTimezone();
        $this->userInvitation->confirmed_at = $invitation->isConfirmed()
            ? (string) $invitation->getConfirmation()
            : null
        ;
        $this->userInvitation->name = $invitation->getName();
        $this->userInvitation->revoked = $invitation->isRevoked() ? 1 : 0;
        $this->userInvitation->user_id = $invitation->getCreator()->getId();
        $this->userInvitation->remark = $invitation->getRemark();
        $this->userInvitation->created_at = strval($invitation->getTraceableTime()->getCreatedAt());
        $this->userInvitation->updated_at = $invitation->getTraceableTime()->getUpdatedAt()?->__toString();
        return $this;
    }

    /**
     * Persist the entity UserInvitation to a database row.
     *
     * @param UserInvitationEntity $invitation
     * @return $this
     */
    public function persistEntity(UserInvitationEntity $invitation): static
    {
        $this->userInvitation->id = $invitation->id();
        return $this->persist($invitation->domain());
    }
}
