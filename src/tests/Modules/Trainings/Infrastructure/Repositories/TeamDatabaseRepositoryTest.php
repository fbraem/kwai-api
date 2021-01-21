<?php
declare(strict_types=1);

use Illuminate\Support\Collection;
use Kwai\Core\Domain\Entity;
use Kwai\Modules\Trainings\Infrastructure\Repositories\TeamDatabaseRepository;
use Tests\Context;

$context = Context::createContext();

it('can get a team', function () use ($context) {
    $repo = new TeamDatabaseRepository($context->db);
    try {
        $teams = $repo->getById(1);
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
    expect($teams)
        ->toBeInstanceOf(Collection::class)
        ->and($teams->first())
        ->toBeInstanceOf(Entity::class)
    ;
})
    ->skip(!Context::hasDatabase(), 'No database available')
;

it('can get all teams', function () use ($context) {
    $repo = new TeamDatabaseRepository($context->db);
    try {
        $teams = $repo->getAll();
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
    expect($teams)
        ->toBeInstanceOf(Collection::class)
        ->and($teams->count())
        ->toBeGreaterThan(0)
    ;
})
    ->skip(!Context::hasDatabase(), 'No database available')
;
