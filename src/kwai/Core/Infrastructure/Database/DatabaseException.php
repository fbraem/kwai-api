<?php
/**
 * @package Kwai
 * @subpackage Core
 */
declare(strict_types = 1);

namespace Kwai\Core\Infrastructure\Database;

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
     * Query, if available.
     * @var string|null
     */
    private $query;

    /**
     * DatabaseException constructor
     * @param \PDOException $exception The PDO exception
     * @param string|null $query       The query, if available
     */
    public function __construct(\PDOException $exception, ?string $query = null)
    {
        parent::__construct();
        $this->wrappedException = $exception;
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
                . strval($this->wrappedException)
                . ': '
                . $this->query
            ;
        }
        return __CLASS__ . ': ' . strval($this->wrappedException);
    }
}
