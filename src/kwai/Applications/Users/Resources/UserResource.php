<?php
/**
 * @package Applications
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Applications\Users\Resources;

use Kwai\Core\Domain\Entity;
use Kwai\JSONAPI;
use Kwai\Modules\Users\Domain\User;

/**
 * Class UserResource
 */
#[JSONAPI\Resource(type: 'users', id: 'getId')]
class UserResource
{
    /**
     * @param Entity<User> $user
     * @param Entity|null  $currentUser
     */
    public function __construct(
        private Entity $user,
        private ?Entity $currentUser = null
    ) {
    }

    public function getId(): string
    {
        return (string) $this->user->getUuid();
    }

    #[JSONAPI\Attribute(name: 'email')]
    public function getEmail(): string
    {
        return (string) $this->user->getEmailAddress();
    }

    #[JSONAPI\Attribute(name: 'username')]
    public function getUsername(): string
    {
        return (string) $this->user->getUsername();
    }

    #[JSONAPI\Attribute(name: 'first_name')]
    public function getFirstName(): string
    {
        return (string) $this->user->getUsername()->getFirstName();
    }

    #[JSONAPI\Attribute(name: 'last_name')]
    public function getLastName(): string
    {
        return (string) $this->user->getUsername()->getLastName();
    }

    #[JSONAPI\Attribute(name: 'remark')]
    public function getRemark(): ?string
    {
        return $this->user->getRemark();
    }

    #[JSONAPI\Attribute(name: 'created_at')]
    public function getCreationDate(): string
    {
        return (string) $this->user->getTraceableTime()->getCreatedAt();
    }

    #[JSONAPI\Attribute(name: 'updated_at')]
    public function getUpdateDate(): ?string
    {
        return $this->user->getTraceableTime()->getUpdatedAt()?->__toString();
    }

    #[JSONAPI\Relationship(name: 'roles')]
    public function getRoles(): array
    {
        return $this->user->getRoles()->map(
            fn ($role) => new RoleResource($role)
        )->toArray();
    }

    #[JSONAPI\Attribute(name: 'owner')]
    public function isOwner(): bool
    {
        if ($this->currentUser) {
            return $this->currentUser->id() === $this->user->id();
        }
        return false;
    }
}
