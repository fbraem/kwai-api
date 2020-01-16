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
 * Waiting for generics in PHP, to make the $domain object typesafe.
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
     * @var mixed
     */
    private $domain;

    /**
     * Constructor
     *
     * @param int $id       The id of the entity
     * @param DomainEntity $domain The domain entity
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
    public function id(): ?int
    {
        return $this->id;
    }

    public function __call($method, $args)
    {
        return $this->domain->{$method}($args);
    }
}
