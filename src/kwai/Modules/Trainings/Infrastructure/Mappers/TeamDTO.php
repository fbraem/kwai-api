<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Infrastructure\Mappers;

use Kwai\Modules\Trainings\Domain\Team;
use Kwai\Modules\Trainings\Domain\TeamEntity;
use Kwai\Modules\Trainings\Infrastructure\TeamsTable;

/**
 * Class TeamDTO
 */
class TeamDTO
{
    public function __construct(
        public TeamsTable $team = new TeamsTable()
    ) {
    }

    public function create(): Team
    {
        return new Team(
            name: $this->team->name
        );
    }

    public function createEntity(): TeamEntity
    {
        return new TeamEntity(
            $this->team->id,
            $this->create()
        );
    }
}
