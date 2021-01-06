<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\Domain\Exceptions;

use Exception;

/**
 * Class UserNotFoundException
 *
 * Raised when a user couldn't be found.
 */
class UserNotFoundException extends Exception
{
    /**
     * UserNotFoundException constructor.
     *
     * @param int $id
     */
    public function __construct(private int $id)
    {
        parent::__construct('User not found');
    }

    public function __toString()
    {
        return __CLASS__ . ': User(' . $this->id . ') not found';
    }
}
