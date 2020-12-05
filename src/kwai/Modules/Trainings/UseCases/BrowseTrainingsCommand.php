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
    public ?int $limit = null;
    public ?int $offset = null;

    public ?int $year = null;
    public ?int $month = null;

    // By default, only the active trainings will be returned.
    public bool $active = true;
}