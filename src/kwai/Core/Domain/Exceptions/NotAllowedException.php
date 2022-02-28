<?php
/**
 * @package Kwai
 * @subpackage Core
 */
declare(strict_types = 1);

namespace Kwai\Core\Domain\Exceptions;

use Exception;

/**
 * NotAllowedException class. This exception is thrown
 * when an action is not allowed.
 */
class NotAllowedException extends Exception
{
    /**
     * NotAllowedException constructor
     * @param string $subject
     * @param string $action
     */
    public function __construct(
        string $subject,
        string $action,
        string $message = ''
    ) {
        $fullMessage = $action . ' not allowed on ' . $subject;
        if ($message) {
            $fullMessage .= ': ' . $message;
        }
        parent::__construct($fullMessage);
    }

    /**
     * Returns a string representation of this exception.
     * @return string
     */
    public function __toString()
    {
        return __CLASS__ . ': ' . $this->getMessage();
    }
}
