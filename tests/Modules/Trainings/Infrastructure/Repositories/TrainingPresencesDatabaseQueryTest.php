<?php

declare(strict_types=1);

use Illuminate\Support\Collection;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Modules\Trainings\Infrastructure\Repositories\TrainingPresencesDatabaseQuery;
use Tests\Context;

$context = Context::createContext();

it('can query training presences', function () use ($context) {
    $query = new TrainingPresencesDatabaseQuery($context->db);
    try {
        $presences = $query->execute();
    } catch (QueryException $e) {
        $this->fail((string) $e);
    }
    expect($presences)
        ->toBeInstanceOf(Collection::class)
    ;
})
    ->skip(!Context::hasDatabase(), 'No database available')
;
