<?php
/**
 * @package Applications
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Applications\Users\Resources;

use Kwai\Core\Domain\Permission;
use Kwai\JSONAPI;
use Kwai\Modules\Users\Domain\RuleEntity;

/**
 * Class RuleResource
 */
#[JSONAPI\Resource(type: 'rules', id: 'getId')]
class RuleResource
{
    /**
     * @param RuleEntity $rule
     */
    public function __construct(
        private RuleEntity $rule
    ) {
    }

    public function getId(): string
    {
        return (string) $this->rule->id();
    }

    #[JSONAPI\Attribute(name: 'name')]
    public function getName(): string
    {
        return $this->rule->getName();
    }

    #[JSONAPI\Attribute(name: 'subject')]
    public function getSubject(): string
    {
        return $this->rule->getSubject();
    }

    #[JSONAPI\Attribute(name: 'remark')]
    public function getRemark(): ?string
    {
        return $this->rule->getRemark();
    }

    #[JSONAPI\Attribute(name: 'created_at')]
    public function getCreationDate(): string
    {
        return (string) $this->rule->getTraceableTime()->getCreatedAt();
    }

    #[JSONAPI\Attribute(name: 'can_update')]
    public function canUpdate(): bool
    {
        return $this->rule->hasPermission(Permission::CanUpdate);
    }

    #[JSONAPI\Attribute(name: 'can_create')]
    public function canCreate(): bool
    {
        return $this->rule->hasPermission(Permission::CanCreate);
    }

    #[JSONAPI\Attribute(name: 'can_view')]
    public function canView(): bool
    {
        return $this->rule->hasPermission(Permission::CanView);
    }

    #[JSONAPI\Attribute(name: 'can_delete')]
    public function canDelete(): bool
    {
        return $this->rule->hasPermission(Permission::CanDelete);
    }

    #[JSONAPI\Attribute(name: 'updated_at')]
    public function getUpdateDate(): ?string
    {
        return $this->rule->getTraceableTime()->getUpdatedAt()?->__toString();
    }
}
