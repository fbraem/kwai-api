<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Repositories;

use Exception;
use Throwable;

/**
 * Class RepositoryException
 */
class RepositoryException extends Exception
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
        return __CLASS__ . ': ' . strval($this->getPrevious());
    }
}
