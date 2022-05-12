<?php
/**
 * @package Applications
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Applications\Users\Resources;

use Kwai\JSONAPI;
use Kwai\Modules\Users\Domain\UserEntity;

/**
 * Class UserResource
 */
#[JSONAPI\Resource(type: ResourceTypes::USERS, id: 'getId')]
class UserResource
{
    /**
     * @param UserEntity      $user
     * @param UserEntity|null $currentUser
     */
    public function __construct(
        private readonly UserEntity $user,
        private readonly ?UserEntity $currentUser = null
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

    #[JSONAPI\Attribute(name: 'owner')]
    public function isOwner(): bool
    {
        if ($this->currentUser) {
            return $this->currentUser->id() === $this->user->id();
        }
        return false;
    }
}
