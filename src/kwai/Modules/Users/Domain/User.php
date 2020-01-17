<?php
/**
 * @package Kwai/Modules
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Domain;

use Kwai\Core\Domain\EmailAddress;
use Kwai\Core\Domain\UniqueId;
use Kwai\Core\Domain\TraceableTime;
use Kwai\Core\Domain\DateTime;
use Kwai\Core\Domain\DomainEntity;

use Kwai\Modules\Users\Domain\ValueObjects\Password;
use Kwai\Modules\Users\Domain\ValueObjects\Username;

/**
 * User Entity
 */
class User implements DomainEntity
{
    /**
     * A UUID of the user.
     * @var UniqueId
     */
    private $uuid;

    /**
     * The emailaddress of the user
     * @var EmailAddress
     */
    private $emailAddress;

    /**
     * Track create & modify times
     * @var TraceableTime
     */
    private $traceableTime;

    /**
     * The timestamp of the last login
     * @var DateTime
     */
    private $lastLogin;

    /**
     * A remark about the user
     * @var string
     */
    private $remark;

    /**
     * The username
     * @var Username
     */
    private $username;

    /**
     * The password of the user
     * @var Password
     */
    private $password;

    /**
     * Constructor.
     * @param  object $props User properties
     */
    public function __construct(object $props)
    {
        $this->uuid = $props->uuid;
        $this->emailAddress = $props->emailAddress;
        $this->traceableTime = $props->traceableTime;
        $this->lastLogin = $props->lastLogin;
        $this->remark = $props->remark;
        $this->username = $props->username;
        $this->password = $props->password;
    }

    /**
     * Verify the password.
     * @param string $password The password to login.
     */
    public function login(string $password): bool
    {
        return $this->password->verify($password);
    }
}
