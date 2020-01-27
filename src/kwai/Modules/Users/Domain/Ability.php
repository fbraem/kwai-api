<?php
/**
 * @package Kwai/Modules
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Domain;

use Kwai\Core\Domain\TraceableTime;
use Kwai\Core\Domain\DomainEntity;

/**
 * Ability Entity
 */
class Ability implements DomainEntity
{
    /**
     * The name of the ability.
     * @var string
     */
    private $name;

    /**
     * A remark
     * @var string
     */
    private $remark;

    /**
     * Track create & modify times
     * @var TraceableTime
     */
    private $traceableTime;

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
        $this->traceableTime = $props->traceableTime;
        $this->remark = $props->remark;
        $this->rules = $props->rules ?? [];
    }

    /**
     * Add a rule to this ability
     * @param Entity $rule
     */
    public function addRule(Entity $rule): void
    {
        if (!is_a($rule->domain(), Rule::class)) {
            throw new InvalidArgumentException('$rule argument is not a Rule class');
        }
        $this->rules[] = $rule;
    }
}