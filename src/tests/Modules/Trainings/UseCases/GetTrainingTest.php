<?php

declare(strict_types=1);

use Kwai\Modules\Trainings\Infrastructure\Repositories\TrainingDatabaseRepository;
use Kwai\Modules\Trainings\UseCases\GetTraining;
use Kwai\Modules\Trainings\UseCases\GetTrainingCommand;
use Tests\Context;

$context = Context::createContext();

it('can get a training', function () use ($context) {
    $command = new GetTrainingCommand();
    $command->id = 1;

    try {
        GetTraining::create(new TrainingDatabaseRepository(
            $context->db
        ))($command);
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
})
    ->skip(!Context::hasDatabase(), 'No database available')
;
