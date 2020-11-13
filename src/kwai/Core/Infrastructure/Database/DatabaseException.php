<?php
/**
 * @package Kwai
 * @subpackage Core
 */
declare(strict_types = 1);

namespace Kwai\Core\Infrastructure\Database;

use Exception;
use PDOException;

/**
 * DatabaseException
 */
class DatabaseException extends Exception
{
    /**
     * Query, if available.
     */
    private ?string $query;

    /**
     * DatabaseException constructor
     * @param PDOException $exception The PDO exception
     * @param string|null $query       The query, if available
     */
    public function __construct(PDOException $exception, ?string $query = null)
    {
        parent::__construct($exception->getMessage(), 0, $exception);
        $this->query = $query;
    }

    /**
     * Returns a string representation of this exception.
     * @return string
     */
    public function __toString()
    {
        if ($this->query) {
            return
                __CLASS__
                . ': '
                . strval($this->getPrevious())
                . ': '
                . $this->query
            ;
        }
        return __CLASS__ . ': ' . strval($this->getPrevious());
    }
}
