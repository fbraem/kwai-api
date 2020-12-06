<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Domain;

use Kwai\Core\Domain\DomainEntity;

/**
 * Class Season
 *
 * Represents a Season
 */
class Season implements DomainEntity
{
    /**
     * Season constructor.
     *
     * @param string $name
     */
    public function __construct(
        private string $name
    ) {
    }

    /**
     * Get the name of the season
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}
