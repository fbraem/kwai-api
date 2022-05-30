<?php
declare(strict_types=1);

use Illuminate\Support\Collection;
use Kwai\Core\Domain\Entity;
use Kwai\Modules\Trainings\Infrastructure\Repositories\SeasonDatabaseRepository;
use Tests\DatabaseTrait;

uses(DatabaseTrait::class);
beforeEach(fn() => $this->withDatabase());

it('can get a season', function () {
    $repo = new SeasonDatabaseRepository($this->db);
    try {
        $season = $repo->getById(1);
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
    expect($season)
        ->toBeInstanceOf(Collection::class)
        ->and($season->first())
        ->toBeInstanceOf(Entity::class)
    ;
})
    ->skip(fn() => !$this->hasDatabase(), 'No database available')
;

it('can get all seasons', function () {
    $repo = new SeasonDatabaseRepository($this->db);
    try {
        $seasons = $repo->getAll();
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
    expect($seasons)
        ->toBeInstanceOf(Collection::class)
        ->and($seasons->count())
        ->toBeGreaterThan(0)
    ;
})
    ->skip(fn() => !$this->hasDatabase(), 'No database available')
;
