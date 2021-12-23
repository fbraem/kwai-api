<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\UseCases;

/**
 * Class BrowseTrainingsCommand
 *
 * Command for the use case Browse Trainings
 */
class BrowseTrainingsCommand
{
    /**
     * Limit the returned number of trainings
     *
     * @var int|null
     */
    public ?int $limit = null;

    /**
     * Set the offset
     *
     * @var int|null
     */
    public ?int $offset = null;

    /**
     * Get only trainings for this year
     *
     * @var int|null
     */
    public ?int $year = null;

    /**
     * Get only trainings for this month
     *
     * @var int|null
     */
    public ?int $month = null;

    /**
     * Get only trainings for this week of the year
     *
     * @var int|null
     */
    public ?int $week = null;

    /**
     * Get only trainings starting from the given date.
     * When no end date is given, the end date will be set to 7 days after
     * this date.
     *
     * @var string|null
     */
    public ?string $start = null;

    /**
     * Get only trainings before the given date.
     * When no start date is given, the start date will be set to the current
     * date.
     *
     * @var string|null
     */
    public ?string $end = null;

    /**
     * Get only trainings for this coach
     *
     * @var int|null
     */
    public ?int $coach = null;

    /**
     * Get only the active trainings
     * By default, only the active trainings will be returned.
     *
     * @var bool
     */
    public bool $active = true;

    /**
     * Get only trainings for this definition
     *
     * @var int|null
     */
    public ?int $definition = null;
}
