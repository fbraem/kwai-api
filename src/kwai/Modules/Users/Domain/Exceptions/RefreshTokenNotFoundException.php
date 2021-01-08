<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\Domain\Exceptions;

use Exception;
use Kwai\Modules\Users\Domain\ValueObjects\TokenIdentifier;

/**
 * Class RefreshTokenNotFoundException
 *
 * Raised when a user couldn't be found.
 */
class RefreshTokenNotFoundException extends Exception
{
    /**
     * RefreshTokenNotFoundException constructor.
     *
     * @param TokenIdentifier $tokenIdentifier
     */
    public function __construct(private TokenIdentifier $tokenIdentifier)
    {
        parent::__construct('User not found');
    }

    public function __toString()
    {
        return __CLASS__ . ': RefreshToken(' . $this->tokenIdentifier . ') not found';
    }
}
