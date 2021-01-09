<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\Domain\Exceptions;

use Exception;

/**
 * Class UserInvitationNotFoundException
 *
 * Raised when a user couldn't be found.
 */
class UserInvitationNotFoundException extends Exception
{
    /**
     * UserInvitationNotFoundException constructor.
     *
     * @param int $id
     */
    public function __construct(private int $id)
    {
        parent::__construct('User invitation not found');
    }

    public function __toString()
    {
        return __CLASS__ . ': UserInvitation(' . $this->id . ') not found';
    }
}
