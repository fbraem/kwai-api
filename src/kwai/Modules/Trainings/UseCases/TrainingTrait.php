<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);


namespace Kwai\Modules\Trainings\UseCases;

/**
 * Trait TrainingTrait
 *
 * Common properties for update/create command
 */
trait TrainingTrait
{
    public string $start_date;

    public string $end_date;

    public string $timezone;

    public bool $active = true;

    public bool $cancelled = false;

    public ?string $location = null;

    public array $contents = [];

    public ?string $remark = null;

    public ?int $definition = null;

    public array $coaches = [];

    public array $teams = [];
}
