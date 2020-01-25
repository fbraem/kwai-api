<?php
/**
 * NotFoundException
 * @package Kwai
 * @subpackage Core
 */
declare(strict_types = 1);

namespace Kwai\Core\Domain\Exceptions;

/**
 * NotCreatedException class. This exception is thrown
 * when an entity couldn't be created.
 */
class NotCreatedException extends \Exception
{
    /**
     * Entity that is not created.
     * @var string
     */
    private $entity;

    /**
     * NotCreatedException constructor
     * @param string $entity  Entity that isn't created.
     */
    public function __construct(string $entity)
    {
        parent::__construct($entity . ' not created');
        $this->entity = $entity;
    }

    public function __toString()
    {
        return __CLASS__ . ': ' . $this->message;
    }
}
