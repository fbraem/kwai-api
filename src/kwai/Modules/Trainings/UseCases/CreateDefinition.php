<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\UseCases;

use Kwai\Core\Domain\ValueObjects\Creator;
use Kwai\Core\Domain\ValueObjects\Location;
use Kwai\Core\Domain\ValueObjects\Time;
use Kwai\Core\Domain\ValueObjects\TimePeriod;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Trainings\Domain\Definition;
use Kwai\Modules\Trainings\Domain\DefinitionEntity;
use Kwai\Modules\Trainings\Domain\Exceptions\SeasonNotFoundException;
use Kwai\Modules\Trainings\Domain\Exceptions\TeamNotFoundException;
use Kwai\Modules\Trainings\Repositories\DefinitionRepository;
use Kwai\Modules\Trainings\Repositories\SeasonRepository;
use Kwai\Modules\Trainings\Repositories\TeamRepository;

/**
 * Class CreateDefinition
 *
 * Use case: create a definition
 */
final class CreateDefinition
{
    /**
     * CreateDefinition constructor.
     *
     * @param DefinitionRepository $definitionRepo
     * @param TeamRepository       $teamRepo
     * @param SeasonRepository     $seasonRepo
     */
    public function __construct(
        private readonly DefinitionRepository $definitionRepo,
        private readonly TeamRepository       $teamRepo,
        private readonly SeasonRepository $seasonRepo
    ) {
    }

    /**
     * Execute the use case
     *
     * @param CreateDefinitionCommand $command
     * @param Creator $creator
     * @return DefinitionEntity
     * @throws RepositoryException
     * @throws SeasonNotFoundException
     * @throws TeamNotFoundException
     */
    public function __invoke(
        CreateDefinitionCommand $command,
        Creator $creator
    ): DefinitionEntity {
        if (isset($command->team_id)) {
            $teams = $this->teamRepo->getById($command->team_id);
            if ($teams->isEmpty()) {
                throw new TeamNotFoundException($command->team_id);
            }
            $team = $teams->first();
        } else {
            $team = null;
        }

        if (isset($command->season_id)) {
            $seasons = $this->seasonRepo->getById($command->season_id);
            if ($seasons->isEmpty()) {
                throw new SeasonNotFoundException($command->season_id);
            }
            $season = $seasons->first();
        } else {
            $season = null;
        }

        $definition = new Definition(
            name: $command->name,
            description: $command->description,
            weekday: $command->weekday,
            period: new TimePeriod(
                Time::createFromString($command->start_time, $command->time_zone),
                Time::createFromString($command->end_time, $command->time_zone)
            ),
            creator: $creator,
            team: $team,
            season: $season,
            active: $command->active,
            location: $command->location ? new Location($command->location) : null,
            remark: $command->remark,
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
    ): CreateDefinition
    {
        return new self(
            $definitionRepo,
            $teamRepo,
            $seasonRepo
        );
    }
}
