<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Repositories;

use Kwai\Core\Domain\Entity;
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
}
