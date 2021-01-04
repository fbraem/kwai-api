<?php
/**
 * @package Kwai/Modules
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
 * Ability Entity
 */
class Ability implements DomainEntity
{
    /**
     * Constructor.
     *
     * @param string             $name
     * @param string|null        $remark
     * @param TraceableTime|null $traceableTime
     * @param Collection|null    $rules
     */
    public function __construct(
        private string $name,
        private ?string $remark = null,
        private ?TraceableTime $traceableTime = null,
        private ?Collection $rules = null
    )
    {
        $this->traceableTime ??= new TraceableTime();
        $this->rules ??= collect();
    }

    /**
     * Add a rule to this ability
     * @param Entity<Rule> $rule
     */
    public function addRule(Entity $rule): void
    {
        if (!is_a($rule->domain(), Rule::class)) {
            throw new InvalidArgumentException('$rule argument is not a Rule class');
        }
        $this->rules[] = $rule;
    }

    /**
     * Returns the name of the ability
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
     * @return string
     */
    public function getRemark(): string
    {
        return $this->remark;
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
