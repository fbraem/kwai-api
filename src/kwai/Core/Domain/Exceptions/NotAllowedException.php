<?php
/**
 * @package Kwai
 * @subpackage Core
 */
declare(strict_types = 1);

namespace Kwai\Core\Domain\Exceptions;

/**
 * NotAllowedException class. This exception is thrown
 * when an action is not allowed.
 */
class NotAllowedException extends \Exception
{
    /**
     * Action that is not allowed
     * @var string
     */
    private $action;

    /**
     * Subject on which the action is not allowed
     * @var string
     */
    private $subject;

    /**
     * NotAuthorizedException constructor
     * @param string $subject
     * @param string $action
     */
    public function __construct(string $subject, string $action)
    {
        parent::__construct($action . ' not allowed on ' . $subject);
        $this->subject = $subject;
        $this->action = $action;
    }

    /**
     * Returns a string representation of this exception.
     * @return string
     */
    public function __toString()
    {
        return __CLASS__ . ': ' . $this->message;
    }
}
