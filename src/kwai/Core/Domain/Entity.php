<?php
/**
 * Entity class
 * @package Kwai
 * @subpackage Core
 */
declare(strict_types = 1);

namespace Kwai\Core\Domain;

/**
 * Entity of the domain. This class is a decorator around a Domain Entity
 * and provides an Id. All method calls are forwarded to the wrapped domain
 * object.
 *
 * @template T of DomainEntity
 * @SuppressWarnings(PHPMD.ShortVariable)
 * @deprecated (Create separate Entity classes with EntityTrait)
 */
class Entity
{
    /**
     * Constructor
     *
     * @param int $id   The id of the entity
     * @param T $domain The domain entity
     */
    public function __construct(
        private readonly int $id,
        private readonly DomainEntity $domain
    ) {
    }

    /**
     * Returns the id.
     * @return int The id of the entity
     */
    public function id(): int
    {
        return $this->id;
    }

    /**
     * Returns the wrapped domain object
     *
     * @return T
     */
    public function domain()
    {
        return $this->domain;
    }

    /**
     * Forward the method call to the wrapped domain object.
     *
     * @param string $method
     * @param array  $args
     * @return mixed
     */
    public function __call(string $method, array $args)
    {
        return $this->domain->{$method}(...$args);
    }
}
