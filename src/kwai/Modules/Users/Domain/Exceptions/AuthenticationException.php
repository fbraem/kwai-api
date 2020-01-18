<?php
/**
 * AuthenticationException
 * @package Kwai
 * @subpackage Core
 */
declare(strict_types = 1);

namespace Kwai\Core\Domain\Exceptions;

/**
 * AuthenticationException class. This exception is thrown
 * when the user can't login or when an accesstoken can't be created..
 */
class AuthenticationException extends \Exception
{
    /**
     * AuthenticationException constructor
     * @param string $message
     */
    public function __construct(string $message = 'Authentication failed')
    {
        parent::__construct($message);
    }

    /**
     * Returns a string representation of the exception.
     * @return string
     */
    public function __toString()
    {
        return __CLASS__ . ': ' . $this->getMessage();
    }
}
