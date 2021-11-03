<?php
declare(strict_types=1);

use Illuminate\Support\Collection;
use Kwai\Core\Domain\Entity;
use Kwai\Modules\Trainings\Infrastructure\Repositories\SeasonDatabaseRepository;
use Tests\Context;

$context = Context::createContext();

it('can get a season', function () use ($context) {
    $repo = new SeasonDatabaseRepository($context->db);
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
    ->skip(!Context::hasDatabase(), 'No database available')
;

it('can get all seasons', function () use ($context) {
    $repo = new SeasonDatabaseRepository($context->db);
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
    ->skip(!Context::hasDatabase(), 'No database available')
;
