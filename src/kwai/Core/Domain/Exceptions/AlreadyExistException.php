<?php
/**
 * @package Kwai
 * @subpackage Core
 */
declare(strict_types = 1);

namespace Kwai\Core\Domain\Exceptions;

use Exception;

/**
 * AllReadyExistException class. This exception is thrown
 * when an entity already exists.
 */
class AlreadyExistException extends Exception
{
    /**
     * Entity that already exists
     */
    private string $entity;

    /**
     * Constructor
     * @param string $entity
     * @param string $message
     */
    public function __construct(string $entity, string $message)
    {
        parent::__construct($message);
        $this->entity = $entity;
    }

    /**
     * Returns a string representation of this exception.
     * @return string
     */
    public function __toString()
    {
        return __CLASS__ . ' - ' . $this->entity . ': ' . $this->getMessage();
    }
}
