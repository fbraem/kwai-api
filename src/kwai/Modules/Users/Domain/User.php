<?php
/**
 * User entity
 *
 * @package Kwai/Modules
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Domain;

use Kwai\Core\Domain\EmailAddress;
use Kwai\Core\Domain\UniqueId;
use Kwai\Core\Domain\TraceableTime;
use Kwai\Core\Domain\DateTime;

/**
 * User entity class
 */
class User
{
    /**
     * A UUID of the user.
     * @var UniqueId
     */
    private $uid;

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
     * Constructor
     */
    public function __construct(
        UniqueId $uid,
        EmailAddress $emailAddress,
        TraceableTime $traceableTime,
        DateTime $lastLogin,
        string $remark,
        Username $username,
        Password $password
    ) {
        $this->uid = $uid;
        $this->emailAddress = $emailAddress;
        $this->traceableTime = $traceableTime;
        $this->lastLogin = $lastLogin;
        $this->remark = $remark;
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * Checks if the email and password are correct.
     *
     * @param EmailAddress $email The emailaddress used to login.
     * @param Password $password The password to login.
     */
    public function login(EmailAddress $email, Password $password): bool
    {
    }
}
