<?php

declare(strict_types=1);

use Illuminate\Support\Collection;
use Kwai\Core\Domain\Entity;
use Kwai\Modules\Trainings\Infrastructure\Repositories\TrainingDatabaseRepository;
use Kwai\Modules\Trainings\UseCases\BrowseTrainings;
use Kwai\Modules\Trainings\UseCases\BrowseTrainingsCommand;
use Tests\Context;

$context = Context::createContext();

it('can browse trainings', function () use ($context) {
    $repo = new TrainingDatabaseRepository($context->db);
    $command = new BrowseTrainingsCommand();
    try {
        [$count, $trainings] = BrowseTrainings::create($repo)($command);
        expect($count)
            ->toBeGreaterThan(0)
        ;
        expect($trainings)
            ->toBeInstanceOf(Collection::class)
            ->and($trainings->count())
            ->toBeGreaterThan(0)
        ;
        expect($trainings->first())
            ->toBeInstanceOf(Entity::class)
        ;
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
})
    ->skip(!Context::hasDatabase(), 'No database available')
;
