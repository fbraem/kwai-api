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
use Kwai\Modules\Trainings\Domain\Exceptions\DefinitionNotFoundException;
use Kwai\Modules\Trainings\Domain\Exceptions\SeasonNotFoundException;
use Kwai\Modules\Trainings\Domain\Exceptions\TeamNotFoundException;
use Kwai\Modules\Trainings\Infrastructure\Repositories\DefinitionDatabaseRepository;
use Kwai\Modules\Trainings\Infrastructure\Repositories\SeasonDatabaseRepository;
use Kwai\Modules\Trainings\Infrastructure\Repositories\TeamDatabaseRepository;
use Kwai\Modules\Trainings\Repositories\DefinitionRepository;
use Kwai\Modules\Trainings\Repositories\SeasonRepository;
use Kwai\Modules\Trainings\Repositories\TeamRepository;

/**
 * Class UpdateDefinition
 *
 * Use case: update a definition
 */
final class UpdateDefinition
{
    /**
     * UpdateDefinition constructor.
     *
     * @param DefinitionRepository $definitionRepo
     * @param TeamRepository       $teamRepo
     * @param SeasonRepository     $seasonRepo
     */
    public function __construct(
        private readonly DefinitionRepository $definitionRepo,
        private readonly TeamRepository       $teamRepo,
        private readonly SeasonRepository     $seasonRepo
    ) {
    }

    /**
     * Factory method
     *
     * @param DefinitionDatabaseRepository $definitionRepo
     * @param TeamDatabaseRepository       $teamRepo
     * @param SeasonDatabaseRepository     $seasonRepo
     * @return UpdateDefinition
     */
    public static function create(
        DefinitionDatabaseRepository $definitionRepo,
        TeamDatabaseRepository $teamRepo,
        SeasonDatabaseRepository $seasonRepo
    ): UpdateDefinition {
        return new self($definitionRepo, $teamRepo, $seasonRepo);
    }

    /**
     * Execute the use case
     *
     * @param UpdateDefinitionCommand $command
     * @param Creator $creator
     * @return DefinitionEntity
     * @throws DefinitionNotFoundException
     * @throws RepositoryException
     * @throws SeasonNotFoundException
     * @throws TeamNotFoundException
     */
    public function __invoke(
        UpdateDefinitionCommand $command,
        Creator $creator
    ): DefinitionEntity {
        $definition = $this->definitionRepo->getById($command->id);

        $traceableTime = $definition->getTraceableTime()->markUpdated();

        $currentTeam = $definition->getTeam();
        if (isset($command->team_id)) {
            if ($currentTeam == null || $command->team_id != $currentTeam->id()) {
                $teams = $this->teamRepo->getById($command->team_id);
                if ($teams->isEmpty()) {
                    throw new TeamNotFoundException($command->team_id);
                }
                $currentTeam = $teams->first();
            }
        } else {
            if ($currentTeam != null) {
                $currentTeam = null;
            }
        }

        $currentSeason = $definition->getSeason();
        if (isset($command->season_id)) {
            if ($currentSeason == null || $command->season_id != $currentSeason->id()) {
                $seasons = $this->seasonRepo->getById($command->season_id);
                if ($seasons->isEmpty()) {
                    throw new SeasonNotFoundException($command->season_id);
                }
                $currentSeason = $seasons->first();
            }
        } else {
            if ($currentSeason != null) {
                $currentSeason = null;
            }
        }

        $updatedDefinition = new DefinitionEntity(
            $definition->id(),
            new Definition(
                name: $command->name,
                description: $command->description,
                weekday: $command->weekday,
                period: new TimePeriod(
                    Time::createFromString(
                        $command->start_time,
                        $command->time_zone
                    ),
                    Time::createFromString(
                        $command->end_time,
                        $command->time_zone
                    )
                ),
                creator: $creator,
                team: $currentTeam,
                season: $currentSeason,
                active: $command->active,
                location: $command->location ? new Location($command->location) : null,
                remark: $command->remark,
                traceableTime: $traceableTime,
            )
        );
        $this->definitionRepo->update($updatedDefinition);

        return $updatedDefinition;
    }
}
