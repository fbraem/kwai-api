<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Infrastructure\Mappers;

use Illuminate\Support\Collection;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\Name;
use Kwai\Core\Domain\ValueObjects\UniqueId;
use Kwai\Core\Domain\ValueObjects\EmailAddress;
use Kwai\Core\Domain\ValueObjects\TraceableTime;
use Kwai\Core\Domain\ValueObjects\Timestamp;

use Kwai\Modules\Users\Domain\User;
use Kwai\Modules\Users\Infrastructure\UsersTableSchema;

/**
 * Mapper for the entity User
 */
final class UserDTO
{
    /**
     * @param UsersTableSchema       $user
     * @param Collection<AbilityDTO> $abilities
     */
    public function __construct(
        public UsersTableSchema $user = new UsersTableSchema(),
        public Collection $abilities = new Collection()
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
            abilities: $this->abilities->map(
                fn(AbilityDTO $dto) => $dto->createEntity()
            ),
            traceableTime: new TraceableTime(
                Timestamp::createFromString($this->user->created_at),
                $this->user->updated_at
                    ? Timestamp::createFromString($this->user->updated_at)
                    : null
            ),
            remark: $this->user->remark,
            username: new Name(
                $this->user->first_name,
                $this->user->last_name
            ),
            member: $this->user->member_id
        );
    }

    /**
     * Create a User entity from a database row.
     *
     * @return Entity<User>
     */
    public function createEntity(): Entity
    {
        return new Entity(
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
    public function persist(User $user): static
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
        $this->abilities = $user->getAbilities()->map(
            fn(Entity $ability) => (new AbilityDTO())->persistEntity($ability)
        );
        return $this;
    }

    public function persistEntity(Entity $user): static
    {
        $this->user->id = $user->id();
        return $this->persist($user->domain());
    }
}
