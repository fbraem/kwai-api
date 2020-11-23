<?php

declare(strict_types=1);

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\Creator;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Trainings\Domain\Coach;
use Kwai\Modules\Trainings\Domain\Exceptions\CoachNotFoundException;
use Kwai\Modules\Trainings\Infrastructure\Repositories\CoachDatabaseRepository;
use Tests\Context;

$context = Context::createContext();

it('can get a coach', function () use ($context) {
    $repo = new CoachDatabaseRepository($context->db);
    try {
        $coach = $repo->getById(1);
        expect($coach)
            ->toBeInstanceOf(Entity::class)
        ;
        expect($coach->domain())
            ->toBeInstanceOf(Coach::class)
        ;
    } catch (CoachNotFoundException $e) {
        $this->assertTrue(false, (string) $e);
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
        $coaches = $query->execute();
        expect($coaches)
            ->toBeArray()
        ;
        $coach = current($coaches);
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
