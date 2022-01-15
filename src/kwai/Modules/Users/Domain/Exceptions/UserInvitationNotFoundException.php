<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\Domain\Exceptions;

use Exception;
use Kwai\Core\Domain\ValueObjects\UniqueId;

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
     * @param UniqueId $uuid
     */
    public function __construct(private UniqueId $uuid)
    {
        parent::__construct('User invitation not found');
    }

    public function __toString(): string
    {
        return __CLASS__ . ': UserInvitation(' . $this->uuid . ') not found';
    }
}
