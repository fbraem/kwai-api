<?php
/**
 * @package Modules
 * @subpackage Club
 */
declare(strict_types=1);

namespace Kwai\Modules\Club\Domain;

use Kwai\Core\Domain\DomainEntity;
use Kwai\Core\Domain\ValueObjects\TraceableTime;

/**
 * Class Team
 *
 * Represents a team of a club.
 */
class Team implements DomainEntity
{
    /**
     * Constructor.
     */
    public function __construct(
        private string $name,
        private bool $active = true,
        private ?string $remark = null,
        private ?TraceableTime $traceableTime = null
    ) {
        $this->traceableTime ??= new TraceableTime();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getRemark(): ?string
    {
        return $this->remark;
    }

    public function getTraceableTime(): TraceableTime
    {
        return $this->traceableTime;
    }

    public function isActive(): bool
    {
        return $this->active;
    }
}
