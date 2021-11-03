<?php

declare(strict_types=1);

use Illuminate\Support\Collection;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\Name;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Trainings\Domain\Coach;
use Kwai\Modules\Trainings\Infrastructure\Repositories\CoachDatabaseRepository;
use Tests\Context;

$context = Context::createContext();

it('can get a coach', function () use ($context) {
    $repo = new CoachDatabaseRepository($context->db);
    try {
        $coaches = $repo->getById(1);
        expect($coaches)
            ->toBeInstanceOf(Collection::class)
            ->and($coaches->first())
            ->toBeInstanceOf(Entity::class)
            ->and($coaches->first()->getName())
            ->toBeInstanceOf(Name::class)
            ->and($coaches->first()->getName()->getFirstName())
            ->toBeString()
        ;
    } catch (RepositoryException $e) {
        $this->assertTrue(false, (string) $e);
    }
})
    ->skip(!Context::hasDatabase(), 'No database available')
;

it('can get all active coaches', function () use ($context) {
    $repo = new CoachDatabaseRepository($context->db);
    try {
        $query = $repo->createQuery();
        $query->filterActive(true);
        $coaches = $repo->getAll($query);
        expect($coaches)
            ->toBeInstanceOf(Collection::class)
        ;
        $coach = $coaches->first();
        expect($coach)
            ->toBeInstanceOf(Entity::class)
            ->and($coach->domain())
            ->toBeInstanceOf(Coach::class)
        ;
    } catch (RepositoryException $e) {
        $this->assertTrue(false, (string) $e);
    } catch (QueryException $e) {
        $this->assertTrue(false, (string) $e);
    }
})
    ->skip(!Context::hasDatabase(), 'No database available')
;
