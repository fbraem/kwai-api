<?php

namespace Domain;

/**
 * Exception thrown when a record is not found.
 */
class NotFoundException extends \Exception
{
    public function __construct($message, \Exception $prev)
    {
        parent::__construct($message, 0, $prev);
    }

    public function __toString()
    {
        return __CLASS__ . ': ' . $this->message . PHP_EOL;
    }
}
