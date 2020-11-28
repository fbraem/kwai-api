<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\UseCases;

/**
 * Class BrowseTrainingCommand
 *
 * Command for the use case Browse Trainings
 */
class BrowseTrainingCommand
{
    public ?int $limit = null;
    public ?int $offset = null;

    public ?int $year = null;
    public ?int $month = null;
}
