<?php
/**
 * @package Modules
 * @subpackage Club
 */
declare(strict_types=1);

namespace Kwai\Modules\Club\Domain;

use Kwai\Core\Domain\DomainEntity;
use Kwai\Core\Domain\ValueObjects\Name;
use Kwai\Core\Domain\ValueObjects\TraceableTime;

/**
 * Class Member
 *
 * Represents a member of a club.
 */
class Member implements DomainEntity
{
    /**
     * Constructor.
     */
    public function __construct(
        private Name $name,
        private ?TraceableTime $traceableTime = null
    ) {
        $this->traceableTime ??= new TraceableTime();
    }

    public function getName(): Name
    {
        return $this->name;
    }

    public function getTraceableTime(): TraceableTime
    {
        return $this->traceableTime;
    }
}
