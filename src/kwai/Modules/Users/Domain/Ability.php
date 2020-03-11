<?php
/**
 * @package Kwai/Modules
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Domain;

use InvalidArgumentException;
use Kwai\Core\Domain\TraceableTime;
use Kwai\Core\Domain\DomainEntity;
use Kwai\Core\Domain\Entity;

/**
 * Ability Entity
 */
class Ability implements DomainEntity
{
    /**
     * The name of the ability.
     */
    private string $name;

    /**
     * A remark
     */
    private ?string $remark;

    /**
     * Track create & modify times
     */
    private TraceableTime $traceableTime;

    /**
     * The rules associated with this ability
     * @var array Rule[]
     */
    private $rules;

    /**
     * Constructor.
     * @param  object $props Ability properties
     */
    public function __construct(object $props)
    {
        $this->name = $props->name;
        $this->traceableTime = $props->traceableTime ?? new TraceableTime();
        $this->remark = $props->remark;
        $this->rules = $props->rules ?? [];
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
     * @return Rule[]
     */
    public function getRules(): array
    {
        return $this->rules;
    }
}
