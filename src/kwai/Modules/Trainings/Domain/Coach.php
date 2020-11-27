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
     * The name of the coach
     */
    private Name $name;

    /**
     * Coach constructor.
     *
     * @param object $props
     */
    public function __construct(object $props)
    {
        $this->name = $props->name;
    }

    /**
     * Get the name of the coach
     */
    public function getName(): Name
    {
        return $this->name;
    }
}
