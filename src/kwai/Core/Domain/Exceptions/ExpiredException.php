<?php
/**
 * @package Kwai
 * @subpackage Core
 */
declare(strict_types = 1);

namespace Kwai\Core\Domain\Exceptions;

use Exception;

/**
 * ExpiredException. This exception is thrown
 * when an entity is expired.
 */
class ExpiredException extends Exception
{
    /**
     * Entity that is expired.
     */
    private string $entity;

    /**
     * ExpiredException constructor
     * @param string $entity  Entity that is expired.
     */
    public function __construct(string $entity)
    {
        parent::__construct($entity . ' is expired');
        $this->entity = $entity;
    }

    public function __toString()
    {
        return __CLASS__ . ': ' . $this->getMessage();
    }
}
