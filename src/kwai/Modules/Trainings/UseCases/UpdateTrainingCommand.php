<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\UseCases;

/**
 * Class UpdateTrainingCommand
 *
 * Command for the use case Update Training
 */
final class UpdateTrainingCommand
{
    public int $id;

    use TrainingTrait;
}
