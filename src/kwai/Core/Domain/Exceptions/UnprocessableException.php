<?php
/**
 * @package Kwai
 * @subpackage Core
 */
declare(strict_types = 1);

namespace Kwai\Core\Domain\Exceptions;

use Exception;

/**
 * UnprocessableException. This exception is thrown
 * when a use case can't be executed.
 */
class UnprocessableException extends Exception
{
    /**
     * UnprocessableException constructor
     * @param string $message
     */
    public function __construct(string $message)
    {
        parent::__construct($message);
    }

    public function __toString()
    {
        return __CLASS__ . ': ' . $this->getMessage();
    }
}
