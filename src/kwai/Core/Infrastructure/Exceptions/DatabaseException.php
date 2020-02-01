<?php
/**
 * @package Kwai
 * @subpackage Core
 */
declare(strict_types = 1);

namespace Kwai\Core\Infrastructure\Exceptions;

/**
 * DatabaseException
 */
class DatabaseException extends \Exception
{
    /**
     * The wrapped PDO exception
     * @var \PDOException
     */
    private $wrappedException;

    /**
     * DatabaseException constructor
     */
    public function __construct(\PDOException $exception)
    {
        parent::__construct();
        $this->wrappedException = $exception;
    }

    /**
     * Returns a string representation of this exception.
     * @return string
     */
    public function __toString()
    {
        return __CLASS__ . ': ' . $this->wrappedException->toString();
    }
}
