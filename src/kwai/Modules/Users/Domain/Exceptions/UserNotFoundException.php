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
 * Class UserNotFoundException
 *
 * Raised when a user couldn't be found.
 */
class UserNotFoundException extends Exception
{
    /**
     * UserNotFoundException constructor.
     *
     * @param int|UniqueId $id
     */
    public function __construct(private int|UniqueId $id)
    {
        parent::__construct('User not found');
    }

    public function __toString()
    {
        return __CLASS__ . ': User(' . $this->id . ') not found';
    }
}
