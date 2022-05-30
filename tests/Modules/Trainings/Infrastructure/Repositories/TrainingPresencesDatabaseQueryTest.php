<?php
declare(strict_types=1);

use Illuminate\Support\Collection;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Modules\Trainings\Infrastructure\Repositories\TrainingPresencesDatabaseQuery;
use Tests\DatabaseTrait;

uses(DatabaseTrait::class);
beforeEach(fn() => $this->withDatabase());

it('can query training presences', function () {
    $query = new TrainingPresencesDatabaseQuery($this->db);
    try {
        $presences = $query->execute();
    } catch (QueryException $e) {
        $this->fail((string) $e);
    }
    expect($presences)
        ->toBeInstanceOf(Collection::class)
    ;
})
    ->skip(fn() => !$this->hasDatabase(), 'No database available')
;
