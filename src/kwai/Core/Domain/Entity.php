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
 */
class Entity
{
    /**
     * The id of the entity
     */
    private int $id;

    /**
     * The real domain object
     * @var T
     */
    private $domain;

    /**
     * Constructor
     * @param int $id   The id of the entity
     * @phpstan-param T $domain The domain entity
     * @param mixed $domain The domain entity
     */
    public function __construct(int $id, DomainEntity $domain)
    {
        $this->id = $id;
        $this->domain = $domain;
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
     * @return T
     * @return mixed
     */
    public function domain()
    {
        return $this->domain;
    }

    /**
     * Forward the method call to the wrapped domain object.
     * @param $method
     * @param $args
     * @return mixed
     */
    public function __call($method, $args)
    {
        return $this->domain->{$method}(...$args);
    }
}
