<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Domain;

use Illuminate\Support\Collection;
use InvalidArgumentException;
use Kwai\Core\Domain\ValueObjects\TraceableTime;
use Kwai\Core\Domain\DomainEntity;
use Kwai\Core\Domain\Entity;

/**
 * Role Entity
 */
final class Role implements DomainEntity
{
    /**
     * Constructor.
     *
     * @param string        $name
     * @param string|null   $remark
     * @param string|null   $description
     * @param TraceableTime $traceableTime
     * @param Collection    $rules
     */
    public function __construct(
        private string $name,
        private ?string $remark = '',
        private ?string $description = '',
        private TraceableTime $traceableTime = new TraceableTime(),
        private Collection $rules = new Collection()
    ) {
    }

    /**
     * Add a rule to this role
     *
     * @param RuleEntity $rule
     */
    public function addRule(RuleEntity $rule): void
    {
        $this->rules[] = $rule;
    }

    /**
     * Returns the name of the role
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the created_at/updated_at timestamps
     * @return TraceableTime
     */
    public function getTraceableTime(): TraceableTime
    {
        return $this->traceableTime;
    }

    /**
     * Returns the remark
     */
    public function getRemark(): ?string
    {
        return $this->remark;
    }

    /**
     * Returns the description
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description ?? '';
    }

    /**
     * Get the associated rules
     *
     * @return Collection
     */
    public function getRules(): Collection
    {
        return $this->rules->collect();
    }
}
