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
     */
    public function filterId(int $id): void;

    /**
     * Add a filter to get trainings for a given year/month.
     *
     * @param int $year
     * @param ?int $month
     */
    public function filterYearMonth(int $year, ?int $month = null): void;

    /**
     * Add a filter to only return the active trainings
     */
    public function filterActive(): void;

    /**
     * Add a filter to only get trainings to which the coach is assigned.
     *
     * @param Entity<Coach> $coach
     */
    public function filterCoach(Entity $coach): void;

    /**
     * Add a filter to only get trainings for a specific team.
     *
     * @param Entity<Team> $team
     */
    public function filterTeam(Entity $team): void;

}
