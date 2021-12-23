<?php
/**
 * @package Modules
 * @subpackage Coaches
 */
declare(strict_types=1);

namespace Kwai\Modules\Coaches\Domain\Exceptions;

use Exception;

/**
 * Class UserNotFoundException
 *
 * Raised when a user can't be found.
 */
class UserNotFoundException extends Exception
{
    private int $id;

    /**
     * UserNotFoundException constructor.
     *
     * @param int $id
     */
    public function __construct(int $id)
    {
        parent::__construct('User not found');
        $this->id = $id;
    }

    public function __toString()
    {
        return __CLASS__ . ': User(' . $this->id . ') not found';
    }
}
