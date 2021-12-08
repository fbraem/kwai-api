<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\Presentation\Resources;

use Kwai\Core\Domain\Entity;
use Kwai\JSONAPI;
use Kwai\Modules\Users\Domain\Ability;

/**
 * Class AbilityResource
 */
#[JSONAPI\Resource(type: 'abilities', id: 'getId')]
class AbilityResource
{
    /**
     * @param Entity<Ability> $ability
     */
    public function __construct(
        private Entity $ability
    ) {
    }

    public function getId(): string
    {
        return (string) $this->ability->id();
    }

    #[JSONAPI\Attribute(name: 'name')]
    public function getName(): string
    {
        return $this->ability->getName();
    }

    #[JSONAPI\Attribute(name: 'remark')]
    public function getRemark(): ?string
    {
        return $this->ability->getRemark();
    }

    #[JSONAPI\Attribute(name: 'created_at')]
    public function getCreationDate(): string
    {
        return (string) $this->ability->getTraceableTime()->getCreatedAt();
    }

    #[JSONAPI\Attribute(name: 'updated_at')]
    public function getUpdateDate(): ?string
    {
        return $this->ability->getTraceableTime()->getUpdatedAt()?->__toString();
    }

    #[JSONAPI\Relationship(name: 'rules')]
    public function getRules(): array
    {
        return $this->ability->getRules()->map(
            fn ($rule) => new RuleResource($rule)
        )->toArray();
    }
}
