<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Infrastructure\Repositories;

use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Kwai\Core\Domain\ValueObjects\Date;
use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Database\DatabaseQuery;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Modules\Trainings\Domain\CoachEntity;
use Kwai\Modules\Trainings\Domain\DefinitionEntity;
use Kwai\Modules\Trainings\Domain\TeamEntity;
use Kwai\Modules\Trainings\Infrastructure\Mappers\TrainingContentDTO;
use Kwai\Modules\Trainings\Infrastructure\Mappers\TrainingDTO;
use Kwai\Modules\Trainings\Infrastructure\TrainingCoachesTable;
use Kwai\Modules\Trainings\Infrastructure\TrainingContentsTable;
use Kwai\Modules\Trainings\Infrastructure\TrainingsTable;
use Kwai\Modules\Trainings\Infrastructure\TrainingTeamsTable;
use Kwai\Modules\Trainings\Infrastructure\UsersTable;
use Kwai\Modules\Trainings\Repositories\TrainingQuery;
use function Latitude\QueryBuilder\criteria;
use function Latitude\QueryBuilder\express;
use function Latitude\QueryBuilder\func;
use function Latitude\QueryBuilder\group;
use function Latitude\QueryBuilder\literal;
use function Latitude\QueryBuilder\on;

/**
 * Class TrainingDatabaseQuery
 */
class TrainingDatabaseQuery extends DatabaseQuery implements TrainingQuery
{
    private bool $includePresences = false;

    /**
     * TrainingDatabaseQuery constructor.
     *
     * @param Connection $db
     */
    public function __construct(
        Connection $db,
    ) {
        parent::__construct(
            $db,
            TrainingsTable::column('id')
        );
    }

    /**
     * @inheritDoc
     */
    protected function initQuery(): void
    {
        $this->query
            ->from(TrainingsTable::name())
            ->join(
                TrainingContentsTable::name(),
                on(
                    TrainingContentsTable::column('training_id'),
                    TrainingsTable::column('id')
                )
            )
            ->leftJoin(
                UsersTable::name(),
                on(
                    UsersTable::column('id'),
                    TrainingContentsTable::column('user_id')
                )
            )
        ;
    }

    /**
     * @inheritDoc
     */
    protected function getColumns(): array
    {
        return [
            ...TrainingsTable::aliases(),
            ...TrainingContentsTable::aliases(),
            ...UsersTable::aliases()
        ];
    }

    /**
     * @inheritDoc
     */
    public function filterId(int $id): self
    {
        $this->query->andWhere(TrainingsTable::field('id')->eq($id));
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterYearMonth(int $year, ?int $month = null): self
    {
        $criteria = criteria(
            "%s = %d",
            func(
                'YEAR',
                TrainingsTable::column('start_date')
            ),
            literal($year)
        );
        if ($month) {
            $criteria = $criteria->and(
                criteria(
                    "%s = %d",
                    func(
                        'MONTH',
                        TrainingsTable::column('start_date')
                    ),
                    literal($month)
                )
            );
        }
        $this->query->andWhere(
            group($criteria)
        );
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterWeek(int $week): TrainingQuery
    {
        $weekStart = CarbonImmutable::now()->week($week);
        $weekStartDate = new Date($weekStart);
        $weekEndDate = new Date($weekStart->endOfWeek());
        return $this->filterBetweenDates($weekStartDate, $weekEndDate);
    }

    /**
     * @inheritDoc
     */
    public function filterBetweenDates(Date $from, Date $to): TrainingQuery
    {
        $this->query->andWhere(
            TrainingsTable::field('start_date')
                ->between($from->format(), $to->addDay()->format())
        );
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterActive(): self
    {
        $this->query->andWhere(
            TrainingsTable::field('active')->eq(true)
        );
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterCoach(CoachEntity $coach): self
    {
        $innerSelect = $this->db->createQueryFactory()->select()
            ->columns(
                TrainingCoachesTable::column('training_id')
            )
            ->from(TrainingCoachesTable::name())
            ->where(
                TrainingCoachesTable::field('coach_id')->eq($coach->id())
            )
        ;
        $criteria = TrainingsTable::field('id')->in(express('%s', $innerSelect));
        $this->query->andWhere(group($criteria));
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterTeam(TeamEntity $team): self
    {
        $innerSelect = $this->db->createQueryFactory()->select()
            ->columns(
                TrainingTeamsTable::column('training_id')
            )
            ->from(TrainingTeamsTable::name())
            ->where(
                TrainingTeamsTable::field('team_id')->eq($team->id())
            )
        ;
        $criteria = TrainingsTable::field('id')->in(express('%s', $innerSelect));
        $this->query->andWhere(group($criteria));
        return $this;
    }

    /**
     * @param int|null $limit
     * @param int|null $offset
     * @return Collection<TrainingDTO>
     * @throws QueryException
     */
    public function execute(?int $limit = null, ?int $offset = null): Collection
    {
        $rows = parent::walk($limit, $offset);
        if ($rows->isEmpty()) {
            return new Collection();
        }

        $trainings = new Collection();
        foreach ($rows as $row) {
            $training = TrainingsTable::createFromRow($row);

            if ($trainings->has($training->id)) {
                $trainingDTO = $trainings->get($training->id);
            } else {
                $trainingDTO = new TrainingDTO($training);
                $trainings->put($training->id, $trainingDTO);
            }
            $trainingDTO->contents->push(
                new TrainingContentDTO(
                    content: TrainingContentsTable::createFromRow($row),
                    user: UsersTable::createFromRow($row)
                )
            );
        }

        // Get all definitions, if linked
        $definitionIdMap = $trainings
            ->filter(static fn(TrainingDTO $dto) => $dto->training->definition_id !== null)
            ->mapWithKeys(static fn(TrainingDTO $dto) => [
                $dto->training->id => $dto->training->definition_id
            ])
        ;
        if ($definitionIdMap->count() > 0) {
            $definitionQuery = new DefinitionDatabaseQuery($this->db);
            $definitionQuery->filterIds($definitionIdMap->values()->unique());
            $definitions = $definitionQuery->execute();
            $definitionIdMap->each(
                static fn(int $definitionId, int $trainingId)
                    => $trainings[$trainingId]->definition = $definitions[$definitionId]
            );
        }

        // Get all seasons, if linked
        $seasonIdMap = $trainings
            ->filter(static fn(TrainingDTO $dto) => $dto->training->season_id !== null)
            ->mapWithKeys(static fn(TrainingDTO $dto) => [
                $dto->training->id => $dto->training->season_id
            ])
        ;
        if ($seasonIdMap->count() > 0) {
            $seasonQuery = new SeasonDatabaseQuery($this->db);
            $seasonQuery->filterId(...$seasonIdMap->values()->unique()->toArray());
            $seasons = $seasonQuery->execute();
            $seasonIdMap->each(
                static fn (int $seasonId, int $trainingId)
                    => $trainings[$trainingId]->season = $seasons[$seasonId]
            );
        }

        $trainingIds = $trainings->keys()->toArray();

        // Get all coaches of the training(s)
        $trainingCoachQuery = new TrainingCoachDatabaseQuery($this->db);
        $trainingCoachQuery->filterOnTrainings($trainingIds);
        $trainingCoaches = $trainingCoachQuery->execute();
        foreach ($trainingIds as $trainingId) {
            if ($trainingCoaches->has($trainingId)) {
                $trainings[$trainingId]->coaches = $trainingCoaches[$trainingId];
            }
        }

        // Get all teams of the training(s)
        $teamQuery = new TrainingTeamDatabaseQuery($this->db);
        $teamQuery->filterOnTrainings($trainingIds);
        $trainingTeams = $teamQuery->execute();
        foreach ($trainingIds as $trainingId) {
            if ($trainingTeams->has($trainingId)) {
                $trainings[$trainingId]->teams = $trainingTeams[$trainingId];
            }
        }

        if ($this->includePresences) {
            // Get all presences
            $presenceQuery = new TrainingPresencesDatabaseQuery($this->db);
            $presenceQuery->filterOnTrainings($trainingIds);
            $presences = $presenceQuery->execute();
            foreach ($trainingIds as $trainingId) {
                if ($presences->has($trainingId)) {
                    $trainings[$trainingId]->presences = $presences[$trainingId];
                }
            }
        }

        return $trainings;
    }

    public function withPresences(): TrainingQuery
    {
        $this->includePresences = true;
        return $this;
    }

    public function orderByDate(): TrainingQuery
    {
        $this->query->orderBy(TrainingsTable::column('start_date'), 'ASC');
        return $this;
    }

    public function filterDefinition(DefinitionEntity $definition): TrainingQuery
    {
        $this->query->andWhere(
            TrainingsTable::field('definition_id')->eq($definition->id())
        );
        return $this;
    }
}
