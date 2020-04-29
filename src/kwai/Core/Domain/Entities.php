<?php
/**
 * @package Kwai
 * @subpackage Core
 */
declare(strict_types=1);

namespace Kwai\Core\Domain;

use ArrayIterator;
use IteratorAggregate;

/**
 * Class Entities
 *
 * Collection of entities. Note that the property count is the number of entities
 * as counted in the database. If this collection is the result of a query
 * with a limit / offset option, then count is not the same as the number
 * of items in the entities collection.
 */
class Entities implements IteratorAggregate
{
    /**
     * Number of entities as counted in the database.
     */
    private int $count;

    /**
     * @var Entity[]
     */
    private array $entities;

    /**
     * Entities constructor.
     *
     * @param int      $count
     * @param Entity[] $entities
     */
    public function __construct(int $count, array $entities)
    {
        $this->count = $count;
        $this->entities = $entities;
    }

    /**
     * Returns the number of entities as counted in the database.
     *
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * @inheritDoc
     */
    public function getIterator()
    {
        return new ArrayIterator($this->entities);
    }
}
