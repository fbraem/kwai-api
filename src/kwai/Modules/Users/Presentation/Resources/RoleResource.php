<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\Presentation\Resources;

use Kwai\Core\Domain\Entity;
use Kwai\JSONAPI;
use Kwai\Modules\Users\Domain\Role;

/**
 * Class RoleResource
 */
#[JSONAPI\Resource(type: 'roles', id: 'getId')]
class RoleResource
{
    /**
     * @param Entity<Role> $role
     */
    public function __construct(
        private Entity $role
    ) {
    }

    public function getId(): string
    {
        return (string) $this->role->id();
    }

    #[JSONAPI\Attribute(name: 'name')]
    public function getName(): string
    {
        return $this->role->getName();
    }

    #[JSONAPI\Attribute(name: 'remark')]
    public function getRemark(): ?string
    {
        return $this->role->getRemark();
    }

    #[JSONAPI\Attribute(name: 'created_at')]
    public function getCreationDate(): string
    {
        return (string) $this->role->getTraceableTime()->getCreatedAt();
    }

    #[JSONAPI\Attribute(name: 'updated_at')]
    public function getUpdateDate(): ?string
    {
        return $this->role->getTraceableTime()->getUpdatedAt()?->__toString();
    }

    #[JSONAPI\Relationship(name: 'rules')]
    public function getRules(): array
    {
        return $this->role->getRules()->map(
            fn ($rule) => new RuleResource($rule)
        )->toArray();
    }
}
