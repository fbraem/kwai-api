<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Repositories;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\Date;
use Kwai\Core\Infrastructure\Repositories\Query;
use Kwai\Modules\Trainings\Domain\Coach;
use Kwai\Modules\Trainings\Domain\Team;

/**
 * Class TrainingQuery
 */
interface TrainingQuery extends Query
{
    /**
     * Add a filter for the given id
     *
     * @param int $id
     * @return TrainingQuery
     */
    public function filterId(int $id): self;

    /**
     * Add a filter to get trainings for a given year/month.
     *
     * @param int  $year
     * @param ?int $month
     * @return TrainingQuery
     */
    public function filterYearMonth(int $year, ?int $month = null): self;

    /**
     * Add a filter to get trainings for the given week.
     *
     * @param int $week
     * @return $this
     */
    public function filterWeek(int $week): self;

    /**
     * Add a filter to get trainings between dates.
     * @param Date $from
     * @param Date $to
     * @return $this
     */
    public function filterBetweenDates(Date $from, Date $to): self;

    /**
     * Add a filter to only return the active trainings
     *
     * @return TrainingQuery
     */
    public function filterActive(): self;

    /**
     * Add a filter to only get trainings to which the coach is assigned.
     *
     * @param Entity<Coach> $coach
     * @return TrainingQuery
     */
    public function filterCoach(Entity $coach): self;

    /**
     * Add a filter to only get trainings for a specific team.
     *
     * @param Entity<Team> $team
     * @return TrainingQuery
     */
    public function filterTeam(Entity $team): self;

    /**
     * When called, the query will also return presences
     *
     * @return $this
     */
    public function withPresences(): self;

    /**
     * Order trainings on start time
     *
     * @return $this
     */
    public function orderByDate(): self;
}
