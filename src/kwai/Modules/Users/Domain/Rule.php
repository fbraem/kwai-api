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
 * Rule Entity
 */
class Rule implements DomainEntity
{
    /**
     * The name of the rule.
     * @var string
     */
    private $name;

    /**
     * The subject of the rule.
     * @var string
     */
    private $subject;

    /**
     * The action of the rule.
     * @var string
     */
    private $action;

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
     * Constructor.
     * @param  object $props Ability properties
     */
    public function __construct(object $props)
    {
        $this->name = $props->name;
        $this->action = $props->action;
        $this->subject = $props->subject;
        $this->traceableTime = $props->traceableTime;
        $this->remark = $props->remark;
    }

    /**
     * Return the action.
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * Get the name of the rule
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Returns the subject
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
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
}
