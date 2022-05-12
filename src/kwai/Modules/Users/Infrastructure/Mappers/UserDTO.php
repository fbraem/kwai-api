<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Infrastructure\Mappers;

use Kwai\Core\Domain\ValueObjects\Name;
use Kwai\Core\Domain\ValueObjects\UniqueId;
use Kwai\Core\Domain\ValueObjects\EmailAddress;
use Kwai\Core\Domain\ValueObjects\TraceableTime;
use Kwai\Core\Domain\ValueObjects\Timestamp;

use Kwai\Modules\Users\Domain\User;
use Kwai\Modules\Users\Domain\UserEntity;
use Kwai\Modules\Users\Infrastructure\UsersTable;

/**
 * Mapper for the entity User
 */
final class UserDTO
{
    /**
     * @param UsersTable          $user
     */
    public function __construct(
        public UsersTable $user = new UsersTable()
    ) {
    }

    /**
     * Create a User domain object from a database row.
     *
     * @return User
     */
    public function create(): User
    {
        return new User(
            uuid: new UniqueId($this->user->uuid),
            emailAddress: new EmailAddress($this->user->email),
            username: new Name(
                $this->user->first_name,
                $this->user->last_name
            ),
            admin: $this->user->admin === 1,
            remark: $this->user->remark ?? '',
            member: $this->user->member_id,
            traceableTime: new TraceableTime(
                Timestamp::createFromString($this->user->created_at),
                $this->user->updated_at
                    ? Timestamp::createFromString($this->user->updated_at)
                    : null
            )
        );
    }

    /**
     * Create a User entity from a database row.
     *
     * @return UserEntity
     */
    public function createEntity(): UserEntity
    {
        return new UserEntity(
            $this->user->id,
            $this->create()
        );
    }

    /**
     * Persist the User domain object to a database row.
     *
     * @param User $user
     * @return $this
     */
    public function persist(User $user): UserDTO
    {
        $this->user->uuid = (string) $user->getUuid();
        $this->user->email = (string) $user->getEmailAddress();
        $this->user->first_name = $user->getUsername()->getFirstName();
        $this->user->last_name = $user->getUsername()->getLastName();
        $this->user->remark = $user->getRemark();
        $this->user->member_id = $user->getMember();
        $this->user->created_at = (string) $user->getTraceableTime()->getCreatedAt();
        if ($user->getTraceableTime()->isUpdated()) {
            $this->user->updated_at = (string) $user->getTraceableTime()->getUpdatedAt();
        }
        $this->user->admin = $user->isAdmin() ? 1 : 0;
        return $this;
    }

    public function persistEntity(UserEntity $user): UserDTO
    {
        $this->user->id = $user->id();
        return $this->persist($user->domain());
    }
}
