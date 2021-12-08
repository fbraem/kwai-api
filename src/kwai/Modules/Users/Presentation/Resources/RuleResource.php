<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\Presentation\Resources;

use Kwai\Core\Domain\Entity;
use Kwai\JSONAPI;
use Kwai\Modules\Users\Domain\Rule;

/**
 * Class RuleResource
 */
#[JSONAPI\Resource(type: 'rules', id: 'getId')]
class RuleResource
{
    /**
     * @param Entity<Rule> $rule
     */
    public function __construct(
        private Entity $rule
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

    #[JSONAPI\Attribute(name: 'action')]
    public function getAction(): string
    {
        return $this->rule->getAction();
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

    #[JSONAPI\Attribute(name: 'updated_at')]
    public function getUpdateDate(): ?string
    {
        return $this->rule->getTraceableTime()->getUpdatedAt()?->__toString();
    }
}
