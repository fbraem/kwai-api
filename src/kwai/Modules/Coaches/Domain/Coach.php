<?php
/**
 * @package Modules
 * @subpackage Coaches
 */
declare(strict_types=1);

namespace Kwai\Modules\Coaches\Domain;

use Kwai\Core\Domain\DomainEntity;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\Creator;
use Kwai\Core\Domain\ValueObjects\TraceableTime;

/**
 * Class Coach
 *
 * Entity that implements a Coach
 */
class Coach implements DomainEntity
{
    public function __construct(
        private Entity $member,
        private string $bio,
        private string $diploma,
        private bool $active,
        private Creator $creator,
        private ?string $remark = null,
        private ?TraceableTime $traceableTime = null
    ) {
    }

    /**
     * Get the associated member
     *
     * @return Entity<Member>
     */
    public function getMember(): Entity
    {
        return $this->member;
    }

    /**
     * Get the bio of the coach
     *
     * @return string
     */
    public function getBio(): string
    {
        return $this->bio;
    }

    /**
     * Get the diploma of the coach
     *
     * @return string
     */
    public function getDiploma(): string
    {
        return $this->diploma;
    }

    /**
     * Is the coach still active?
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * Get a remark
     *
     * @return string
     */
    public function getRemark(): string
    {
        return $this->remark;
    }

    /**
     * Get the user that created the coach
     *
     * @return Creator
     */
    public function getCreator(): Creator
    {
        return $this->creator;
    }

    /**
     * Get the timestamp of creation and last update
     *
     * @return TraceableTime
     */
    public function getTraceableTime(): TraceableTime
    {
        return $this->traceableTime;
    }
}
