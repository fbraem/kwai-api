<?php
/**
 * @package Kwai/Modules
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Domain;

use Kwai\Core\Domain\Permission;
use Kwai\Core\Domain\ValueObjects\TraceableTime;
use Kwai\Core\Domain\DomainEntity;

/**
 * Rule Entity
 */
class Rule implements DomainEntity
{
    /**
     * Rule constructor.
     *
     * @param string             $name
     * @param string             $subject
     * @param int                $permission
     * @param string|null        $remark
     * @param TraceableTime|null $traceableTime
     */
    public function __construct(
        private string $name,
        private string $subject,
        private int $permission = 0,
        private ?string $remark = null,
        private ?TraceableTime $traceableTime = new TraceableTime()
    ) {
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

    public function hasPermission(Permission $permission): bool
    {
        return ($this->permission & $permission->value) === $permission->value;
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
     *
     * @return string|null
     */
    public function getRemark(): ?string
    {
        return $this->remark;
    }

    /**
     * Returns the permission.
     *
     * @return int
     */
    public function getPermission(): int
    {
        return $this->permission;
    }
}
