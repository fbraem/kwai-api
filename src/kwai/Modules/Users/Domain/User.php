<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Domain;

use Kwai\Core\Domain\ValueObjects\EmailAddress;
use Kwai\Core\Domain\ValueObjects\Name;
use Kwai\Core\Domain\ValueObjects\TraceableTime;
use Kwai\Core\Domain\ValueObjects\UniqueId;

/**
 * User Entity
 */
final class User
{
    /**
     * Constructor.
     *
     * @param UniqueId               $uuid
     * @param EmailAddress           $emailAddress
     * @param Name                   $username
     * @param bool                   $admin
     * @param string                 $remark
     * @param int|null               $member
     * @param TraceableTime          $traceableTime
     */
    public function __construct(
        private readonly UniqueId      $uuid,
        private readonly EmailAddress  $emailAddress,
        private readonly Name          $username,
        private bool                   $admin = false,
        private readonly string        $remark = '',
        private readonly ?int          $member = null,
        private readonly TraceableTime $traceableTime = new TraceableTime()
    ) {
    }

    /**
     * Returns the email address.
     * @return EmailAddress
     */
    public function getEmailAddress(): EmailAddress
    {
        return $this->emailAddress;
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
     * Get the remark
     * @return string
     */
    public function getRemark(): string
    {
        return $this->remark;
    }

    /**
     * Get the unique id of the user
     * @return UniqueId
     */
    public function getUuid(): UniqueId
    {
        return $this->uuid;
    }

    /**
     * Get the username
     * @return Name Username
     */
    public function getUsername(): Name
    {
        return $this->username;
    }

    /**
     * Get the id of the associated member (if any)
     *
     * @return int|null
     */
    public function getMember(): ?int
    {
        return $this->member;
    }

    /**
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->admin;
    }
}
