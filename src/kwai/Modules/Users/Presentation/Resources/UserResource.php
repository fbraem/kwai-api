<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\Presentation\Resources;

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
     */
    public function __construct(
        private Entity $user
    ) {
    }

    public function getId(): string
    {
        return (string) $this->user->id();
    }

    #[JSONAPI\Attribute(name: 'email')]
    public function getEmail(): string
    {
        return (string) $this->user->getEmailAddress();
    }

    #[JSONAPI\Attribute(name: 'uuid')]
    public function getUniqueId(): string
    {
        return (string) $this->user->getUuid();
    }

    #[JSONAPI\Attribute(name: 'username')]
    public function getUsername(): string
    {
        return (string) $this->user->getUsername();
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

    #[JSONAPI\Relationship(name: 'abilities')]
    public function getAbilities(): array
    {
        return $this->user->getAbilities()->map(
            fn ($ability) => new AbilityResource($ability)
        )->toArray();
    }
}
