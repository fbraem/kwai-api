<?php
/**
 * @package Kwai
 * @subpackage Core
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Database;

use Exception;
use Throwable;

/**
 * Class QueryException
 */
class QueryException extends Exception
{
    public function __construct($message = "", Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }

    /**
     * Returns a string representation of this exception.
     * @return string
     */
    public function __toString()
    {
        return __CLASS__ . ': ' . $this->getMessage() . PHP_EOL . strval($this->getPrevious());
    }
}
