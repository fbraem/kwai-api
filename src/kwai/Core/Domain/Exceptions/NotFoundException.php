<?php
/**
 * NotFoundException
 * @package Kwai
 * @subpackage Core
 */
declare(strict_types = 1);

namespace Kwai\Core\Domain\Exceptions;

/**
 * NotFoundException class. This exception is thrown
 * when an entity isn't found.
 */
class NotFoundException extends \Exception
{
    /**
     * Entity that isn't found.
     * @var string
     */
    private $entity;

    /**
     * NotFoundException constructor
     * @param string $entity  Entity that isn't found.
     */
    public function __construct(string $entity)
    {
        parent::__construct($entity . ' not found');
        $this->entity = $entity;
    }

    public function __toString()
    {
        return __CLASS__ . ': ' . $this->getMessage();
    }
}
