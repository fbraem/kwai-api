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
 * @phpstan-template T of DomainEntity
 */
class Entity
{
    /**
     * The id of the entity
     * @var int
     */
    private $id;

    /**
     * The real domain object
     * @phpstan-var T
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
     * @phpstan-return T
     * @return mixed
     */
    public function domain()
    {
        return $this->domain;
    }

    public function __call($method, $args)
    {
        return $this->domain->{$method}(...$args);
    }
}
