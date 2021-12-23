<?php
/**
 * @package Modules
 * @subpackage Coaches
 */
declare(strict_types=1);

namespace Kwai\Modules\Coaches\Domain;

use Kwai\Core\Domain\DomainEntity;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\TraceableTime;

/**
 * Class Coach
 *
 * Entity that implements a Coach
 */
class Coach implements DomainEntity
{
    /**
     * @param Entity<Member>     $member
     * @param Entity<User>|null  $user
     */
    public function __construct(
        private Entity $member,
        private bool $active,
        private ?string $bio = null,
        private ?string $diploma = null,
        private ?Entity $user = null,
        private ?string $remark = null,
        private ?TraceableTime $traceableTime = null
    ) {
        $this->traceableTime ??= new TraceableTime();
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
     * @return string|null
     */
    public function getBio(): ?string
    {
        return $this->bio;
    }

    /**
     * Get the diploma of the coach
     *
     * @return string|null
     */
    public function getDiploma(): ?string
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
     * @return string|null
     */
    public function getRemark(): ?string
    {
        return $this->remark;
    }

    /**
     * Returns true, when the coach is also a known user.
     *
     * @return bool
     */
    public function isUser()
    {
        return $this->user !== null;
    }

    /**
     * Return the associated user, when the coach is also a known user.
     *
     * @return Entity<User>|null
     */
    public function getUser(): ?Entity
    {
        return $this->user;
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
