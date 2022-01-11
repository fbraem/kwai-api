<?php
/**
 * @package Core
 * @subpackage Infrastructure
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Mappers;

use Kwai\Core\Domain\DomainEntity;
use Kwai\Core\Domain\Entity;

/**
 * Interface DataTransferObject
 */
interface DataTransferObject
{
    /**
     * Create a domain object from the database row.
     *
     * @return DomainEntity
     */
    public function create(): DomainEntity;

    /**
     * Create an entity from the database row.
     * @return Entity
     */
    public function createEntity(): Entity;
}
