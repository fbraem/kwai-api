<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Infrastructure\Repositories;

use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\Date;
use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Database\DatabaseQuery;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Modules\Trainings\Infrastructure\Tables;
use Kwai\Modules\Trainings\Repositories\TrainingQuery;
use function Latitude\QueryBuilder\criteria;
use function Latitude\QueryBuilder\express;
use function Latitude\QueryBuilder\field;
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
            Tables::TRAININGS()->getColumn('id')
        );
    }

    /**
     * @inheritDoc
     * @noinspection PhpUndefinedFieldInspection
     */
    protected function initQuery(): void
    {
        $this->query
           ->from((string) Tables::TRAININGS())
            ->join(
                (string) Tables::TRAINING_CONTENTS(),
                on(Tables::TRAINING_CONTENTS()->training_id, Tables::TRAININGS()->id)
            )
            ->leftJoin(
                (string) Tables::USERS(),
                on(Tables::USERS()->id, Tables::TRAINING_CONTENTS()->user_id)
            )
        ;
    }

    /**
     * @inheritDoc
     */
    protected function getColumns(): array
    {
        $trainingAliasFn = Tables::TRAININGS()->getAliasFn();
        $contentAliasFn = Tables::TRAINING_CONTENTS()->getAliasFn();
        $creatorAliasFn = Tables::USERS()->getAliasFn();

        return [
            $trainingAliasFn('id'),
            $trainingAliasFn('definition_id'),
            $trainingAliasFn('created_at'),
            $trainingAliasFn('updated_at'),
            $trainingAliasFn('start_date'),
            $trainingAliasFn('end_date'),
            $trainingAliasFn('time_zone'),
            $trainingAliasFn('active'),
            $trainingAliasFn('cancelled'),
            $trainingAliasFn('location'),
            $contentAliasFn('locale'),
            $contentAliasFn('format'),
            $contentAliasFn('title'),
            $contentAliasFn('content'),
            $contentAliasFn('summary'),
            $contentAliasFn('created_at'),
            $contentAliasFn('updated_at'),
            $creatorAliasFn('id'),
            $creatorAliasFn('first_name'),
            $creatorAliasFn('last_name')
        ];
    }

    /**
     * @inheritDoc
     */
    public function filterId(int $id): self
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $this->query->andWhere(
            field(Tables::TRAININGS()->id)->eq($id)
        );
        return $this;
    }

    /**
     * @inheritDoc
     * @noinspection PhpUndefinedFieldInspection
     */
    public function filterYearMonth(int $year, ?int $month = null): self
    {
        $criteria = criteria(
            "%s = %d",
            func(
                'YEAR',
                Tables::TRAININGS()->start_date
            ),
            literal($year)
        );
        if ($month) {
            $criteria = $criteria->and(
                criteria(
                    "%s = %d",
                    func(
                        'MONTH',
                        Tables::TRAININGS()->start_date
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
        $weekStart = CarbonImmutable::now()->week($week)
        $weekStartDate = new Date($weekStart);
        $weekEndDate = new Date($weekStart->endOfWeek());
        return $this->filterBetweenDates($weekStartDate, $weekEndDate);
    }

    /**
     * @inheritDoc
     * @noinspection PhpUndefinedFieldInspection
     */
    public function filterBetweenDates(Date $from, Date $to): TrainingQuery
    {
        $this->query->andWhere(
            field(Tables::TRAININGS()->start_date)->between($from->format(), $to->format())
        );
        return $this;
    }

    /**
     * @inheritDoc
     * @noinspection PhpUndefinedFieldInspection
     */
    public function filterActive(): self
    {
        $this->query->andWhere(
            field(Tables::TRAININGS()->active)->eq(true)
        );
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterCoach(Entity $coach): self
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $innerSelect = $this->db->createQueryFactory()->select()
            ->columns(
                Tables::TRAINING_COACHES()->training_id
            )
            ->from((string) Tables::TRAINING_COACHES())
            ->where(
                field(Tables::TRAINING_COACHES()->coach_id)
                ->eq($coach->id())
            )
        ;
        /** @noinspection PhpUndefinedFieldInspection */
        $criteria = field(Tables::TRAININGS()->id)
            ->in(express('%s', $innerSelect));
        $this->query->andWhere(group($criteria));
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterTeam(Entity $team): self
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $innerSelect = $this->db->createQueryFactory()->select()
            ->columns(
                Tables::TRAINING_TEAMS()->training_id
            )
            ->from((string) Tables::TRAINING_TEAMS())
            ->where(
                field(Tables::TRAINING_TEAMS()->team_id)
                    ->eq($team->id())
            )
        ;
        /** @noinspection PhpUndefinedFieldInspection */
        $criteria = field(Tables::TRAININGS()->id)
            ->in(express('%s', $innerSelect));
        $this->query->andWhere(group($criteria));
        return $this;
    }

    /**
     * @param int|null $limit
     * @param int|null $offset
     * @return Collection
     * @throws QueryException
     */
    public function execute(?int $limit = null, ?int $offset = null): Collection
    {
        $rows = parent::walk($limit, $offset);

        $trainings = new Collection();
        $filters = new Collection([
            Tables::TRAININGS()->getAliasPrefix(),
            Tables::TRAINING_CONTENTS()->getAliasPrefix(),
            Tables::USERS()->getAliasPrefix()
        ]);
        foreach ($rows as $row) {
            [
                $training,
                $content,
                $user
            ] = $row->filterColumns($filters);

            if (!$trainings->has($training['id'])) {
                $trainings->put($training['id'], $training);
                $training->put('contents', new Collection());
            }
            $content['creator'] = $user;
            $trainings[$training['id']]['contents']->push($content);
        }

        if ($rows->isEmpty()) {
            return new Collection();
        }

        // Get all definitions, if available
        $definitionIds = $trainings->pluck('definition_id')->filter();
        if ($definitionIds->count() > 0) {
            $definitionQuery = new DefinitionDatabaseQuery($this->db);
            $definitionQuery->filterIds(
                $trainings->pluck('definition_id')->filter()
            );
            $definitions = $definitionQuery->execute();
            foreach ($trainings as $training) {
                if ($training->has('definition_id')) {
                    $training->put(
                        'definition',
                        $definitions->get($training->get('definition_id'))
                    );
                }
            }
        }

        $trainingIds = $trainings->keys()->toArray();

        // Get all coaches of the training(s)
        $trainingCoachQuery = new TrainingCoachDatabaseQuery($this->db);
        $trainingCoachQuery->filterOnTrainings($trainingIds);
        $trainingCoaches = $trainingCoachQuery->execute();
        foreach ($trainingIds as $trainingId) {
            $trainings[$trainingId]->put(
                'coaches',
                $trainingCoaches[$trainingId] ?? new Collection()
            );
        }

        // Get all teams of the training(s)
        $teamQuery = new TrainingTeamDatabaseQuery($this->db);
        $teamQuery->filterOnTrainings($trainingIds);
        $trainingTeams = $teamQuery->execute();
        foreach ($trainingIds as $trainingId) {
            $trainings[$trainingId]
                ->put('teams', $trainingTeams[$trainingId] ?? new Collection())
            ;
        }

        if ($this->includePresences) {
            // Get all presences
            $presenceQuery = new TrainingPresencesDatabaseQuery($this->db);
            $presenceQuery->filterOnTrainings($trainingIds);
            $presences = $presenceQuery->execute();
            foreach ($trainingIds as $trainingId) {
                $trainings[$trainingId]
                    ->put('presences', $presences[$trainingId] ?? new Collection())
                ;
            }
        }

        return $trainings;
    }

    public function withPresences(): TrainingQuery
    {
        $this->includePresences = true;
        return $this;
    }
}
