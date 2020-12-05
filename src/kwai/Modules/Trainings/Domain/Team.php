<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Domain;

use Kwai\Core\Domain\DomainEntity;

/**
 * Class Team
 *
 * Represents a team.
 */
class Team implements DomainEntity
{
    /**
     * Team constructor.
     *
     * @param string $name
     */
    public function __construct(
        private string $name
    ) {
        $this->name = $name;
    }

    /**
     * Get the name of the team
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}
