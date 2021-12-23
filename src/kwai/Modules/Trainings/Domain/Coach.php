<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Domain;

use Kwai\Core\Domain\DomainEntity;
use Kwai\Core\Domain\ValueObjects\Name;

/**
 * Class Coach
 *
 * Entity that represents a Coach
 */
class Coach implements DomainEntity
{
    /**
     * Coach constructor.
     *
     * @param Name $name
     */
    public function __construct(
        private Name $name
    ) {
    }

    /**
     * Get the name of the coach
     */
    public function getName(): Name
    {
        return $this->name;
    }
}
