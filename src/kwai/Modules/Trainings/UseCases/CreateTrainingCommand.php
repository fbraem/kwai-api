<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\UseCases;

/**
 * Class CreateTrainingCommand
 *
 * Command for the use case Create Command
 */
class CreateTrainingCommand
{
    public string $start_date;

    public string $end_date;

    public string $timezone;
    
    public bool $active = true;

    public bool $cancelled = false;

    public string $location;

    public array $contents = [];

    public string $remark;

    public ?int $definition_id;

    public array $coaches = [];

    public array $teams = [];
}
