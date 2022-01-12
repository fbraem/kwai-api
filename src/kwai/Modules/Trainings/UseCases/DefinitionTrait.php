<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\UseCases;

use Kwai\Core\Domain\ValueObjects\Weekday;

/**
 * Trait DefinitionTrait
 *
 * Common properties for the Update/Create command.
 */
trait DefinitionTrait
{
    /**
     * The name of the definition
     */
    public string $name;

    /**
     * The description of the definition
     */
    public string $description;

    /**
     * The id of a season
     */
    public ?int $season_id = null;

    /**
     * The id of a team
     */
    public ?int $team_id = null;

    /**
     * The weekday
     */
    public Weekday $weekday;

    /**
     * The start time (HH:MM)
     */
    public string $start_time;

    /**
     * The end time (HH:MM)
     */
    public string $end_time;

    /**
     * The timezone for the start/end time
     */
    public string $time_zone;

    /**
     * Is this definition active?
     */
    public bool $active = false;

    /**
     * The location
     */
    public ?string $location = null;

    /**
     * A remark
     */
    public ?string $remark = null;
}
