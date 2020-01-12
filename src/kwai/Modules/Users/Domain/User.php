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

use Kwai\Modules\Users\Domain\ValueObjects\Password;
use Kwai\Modules\Users\Domain\ValueObjects\Username;

/**
 * User aggregate class
 */
class User
{
    /**
     * Unique id of the user.
     * @var int
     */
    private $id;

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
     * An array of access tokens associated with the user.
     * @var AccessToken[]
     */
    private $accessTokens = [];

    /**
     * Private constructor
     */
    private function __construct()
    {
    }

    /**
     * Returns the id of the user.
     * @return int The id of the user.
     */
    public function id(): int
    {
        return $this->id;
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

    /**
     * Add an access token
     * @param AccessToken $token
     */
    public function addAccessToken(AccessToken $token): void
    {
        $this->accessTokens[] = $token;
    }

    public static function create(object $props, int $id = null): User
    {
        $user = new User();
        $user->uuid = $props->uuid;
        $user->emailAddress = $props->emailAddress;
        $user->traceableTime = $props->traceableTime;
        $user->lastLogin = $props->lastLogin;
        $user->remark = $props->remark;
        $user->username = $props->username;
        $user->password = $props->password;
        if ($id) {
            $user->id = $id;
        }
        return $user;
    }
}
