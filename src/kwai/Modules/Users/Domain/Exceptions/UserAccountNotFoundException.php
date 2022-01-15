<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\Domain\Exceptions;

use Exception;
use Kwai\Core\Domain\ValueObjects\EmailAddress;

/**
 * Class UserAccountNotFoundException
 *
 * Raised when a user account couldn't be found.
 */
class UserAccountNotFoundException extends Exception
{
    /**
     * UserAccountNotFoundException constructor.
     *
     * @param EmailAddress $email
     */
    public function __construct(private EmailAddress $email)
    {
        parent::__construct('User account not found');
    }

    public function __toString(): string
    {
        return __CLASS__ . ': UserAccount(' . $this->email . ') not found';
    }
}
