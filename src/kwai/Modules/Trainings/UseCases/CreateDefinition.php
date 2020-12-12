<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\UseCases;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\Creator;
use Kwai\Core\Domain\ValueObjects\Location;
use Kwai\Core\Domain\ValueObjects\Time;
use Kwai\Core\Domain\ValueObjects\TimePeriod;
use Kwai\Core\Domain\ValueObjects\Weekday;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Trainings\Domain\Definition;
use Kwai\Modules\Trainings\Domain\Exceptions\TeamNotFoundException;
use Kwai\Modules\Trainings\Repositories\DefinitionRepository;
use Kwai\Modules\Trainings\Repositories\SeasonRepository;
use Kwai\Modules\Trainings\Repositories\TeamRepository;

/**
 * Class CreateDefinition
 *
 * Use case: create a definition
 */
class CreateDefinition
{
    /**
     * CreateDefinition constructor.
     *
     * @param DefinitionRepository $definitionRepo
     * @param TeamRepository       $teamRepo
     * @param SeasonRepository     $seasonRepo
     */
    public function __construct(
        private DefinitionRepository $definitionRepo,
        private TeamRepository $teamRepo,
        private SeasonRepository $seasonRepo
    ) {
    }

    /**
     * Execute the use case
     *
     * @param CreateDefinitionCommand $command
     * @param Creator                 $creator
     * @return Entity<Definition>
     * @throws QueryException
     * @throws RepositoryException
     * @throws TeamNotFoundException
     */
    public function __invoke(
        CreateDefinitionCommand $command,
        Creator $creator
    ): Entity {
        $team = isset($command->team_id) ?
            $this->teamRepo->getById($command->team_id) : null;

        $season = isset($command->season_id) ?
            $this->teamRepo->getById($command->season_id) : null;

        $definition = new Definition(
            name: $command->name,
            description: $command->description,
            weekday: new Weekday($command->weekday),
            period: new TimePeriod(
                Time::createFromString($command->start_time, $command->time_zone),
                Time::createFromString($command->end_time, $command->time_zone)
            ),
            active: $command->active,
            team: $team,
            season: $season,
            location: new Location($command->location),
            remark: $command->remark,
            creator: $creator,
        );

        return $this->definitionRepo->create($definition);
    }

    /**
     * Factory method for this use case.
     *
     * @param DefinitionRepository $definitionRepo
     * @param TeamRepository       $teamRepo
     * @param SeasonRepository     $seasonRepo
     * @return CreateDefinition
     */
    public static function create(
        DefinitionRepository $definitionRepo,
        TeamRepository $teamRepo,
        SeasonRepository $seasonRepo
    ) {
        return new self(
            $definitionRepo,
            $teamRepo,
            $seasonRepo
        );
    }
}
