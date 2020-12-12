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
use Kwai\Modules\Trainings\Domain\Exceptions\DefinitionNotFoundException;
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
class UpdateDefinition
{
    /**
     * UpdateDefinition constructor.
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
    ) {
        return new self($definitionRepo, $teamRepo, $seasonRepo);
    }

    /**
     * Execute the use case
     *
     * @param UpdateDefinitionCommand $command
     * @param Creator                 $creator
     * @return Entity<Definition>
     * @throws DefinitionNotFoundException
     * @throws QueryException
     * @throws RepositoryException
     * @throws TeamNotFoundException
     */
    public function __invoke(
        UpdateDefinitionCommand $command,
        Creator $creator
    ): Entity {
        $definition = $this->definitionRepo->getById($command->id);

        /** @noinspection PhpUndefinedMethodInspection */
        $traceableTime = $definition->getTraceableTime()->markUpdated();

        /** @noinspection PhpUndefinedMethodInspection */
        $currentTeam = $definition->getTeam();
        if (isset($command->team_id)) {
            if ($currentTeam == null || $command->team_id != $currentTeam->id()) {
                $currentTeam = $this->teamRepo->getById($command->team_id);
            }
        } else {
            if ($currentTeam != null) {
                $currentTeam = null;
            }
        }

        /** @noinspection PhpUndefinedMethodInspection */
        $currentSeason = $definition->getSeason();
        if (isset($command->season_id)) {
            if ($currentSeason == null || $command->season_id != $currentSeason->id()) {
                $currentSeason = $this->teamRepo->getById($command->season_id);
            }
        } else {
            if ($currentSeason != null) {
                $currentSeason = null;
            }
        }

        $updatedDefinition = new Entity(
            $definition->id(),
            new Definition(
                name: $command->name,
                description: $command->description,
                weekday: new Weekday($command->weekday),
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
                active: $command->active,
                team: $currentTeam,
                season: $currentSeason,
                location: new Location($command->location),
                remark: $command->remark,
                traceableTime: $traceableTime,
                creator: $creator,
            )
        );
        $this->definitionRepo->update($updatedDefinition);

        return $updatedDefinition;
    }
}
