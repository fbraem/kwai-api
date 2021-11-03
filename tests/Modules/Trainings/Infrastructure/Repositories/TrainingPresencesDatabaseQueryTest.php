<?php

declare(strict_types=1);

use Illuminate\Support\Collection;
use Kwai\Modules\Trainings\Infrastructure\Repositories\TrainingPresencesDatabaseQuery;
use Tests\Context;

$context = Context::createContext();

it('can query training presences', function () use ($context) {
    $query = new TrainingPresencesDatabaseQuery($context->db);
    $presences = $query->execute();
    expect($presences)
        ->toBeInstanceOf(Collection::class)
    ;
})
    ->skip(!Context::hasDatabase(), 'No database available')
;
