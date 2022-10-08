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
 * Class UserRecoveryExpiredException
 *
 * Raised when a user recovery couldn't be found.
 */
class UserRecoveryExpiredException extends Exception
{
    /**
     * UserRecoveryExpiredException constructor.
     *
     * @param UniqueId $uuid
     */
    public function __construct(private UniqueId $uuid)
    {
        parent::__construct('User recovery is expired');
    }

    public function __toString(): string
    {
        return __CLASS__ . ': UserRecovery(' . $this->uuid . ') is expired';
    }
}
