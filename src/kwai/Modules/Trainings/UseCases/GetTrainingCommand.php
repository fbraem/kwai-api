<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\UseCases;

/**
 * Class GetTrainingCommand
 *
 * Command for the GetTraining use case.
 */
final class GetTrainingCommand
{
    public int $id;

    public bool $withPresences = false;
}
