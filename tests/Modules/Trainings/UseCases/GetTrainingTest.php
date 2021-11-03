<?php

declare(strict_types=1);

use Kwai\Core\Domain\Entity;
use Kwai\Modules\Trainings\Infrastructure\Repositories\TrainingDatabaseRepository;
use Kwai\Modules\Trainings\UseCases\GetTraining;
use Kwai\Modules\Trainings\UseCases\GetTrainingCommand;
use Tests\Context;

$context = Context::createContext();

it('can get a training', function () use ($context) {
    $command = new GetTrainingCommand();
    $command->id = 1;

    try {
        $training = GetTraining::create(new TrainingDatabaseRepository(
            $context->db
        ))($command);
        expect($training)
            ->toBeInstanceOf(Entity::class)
        ;
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
})
    ->skip(!Context::hasDatabase(), 'No database available')
;
