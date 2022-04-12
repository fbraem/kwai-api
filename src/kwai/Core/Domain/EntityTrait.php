<?php
/**
 * @package Core
 * @subpackage Domain
 */
declare(strict_types = 1);

namespace Kwai\Core\Domain;


Trait EntityTrait
{
    /**
     * Returns the id.
     * @return int The id of the entity
     */
    public function id(): int
    {
        return $this->id;
    }

    abstract public function domain();

    /**
     * Forward the method call to the wrapped domain object.
     *
     * @param string $method
     * @param array  $args
     * @return mixed
     */
    public function __call(string $method, array $args): mixed
    {
        return $this->domain->{$method}(...$args);
    }
}
