<?php
declare(strict_types=1);

use Illuminate\Support\Collection;
use Kwai\Core\Domain\ValueObjects\Name;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Trainings\Domain\CoachEntity;
use Kwai\Modules\Trainings\Infrastructure\Repositories\CoachDatabaseRepository;
use Tests\DatabaseTrait;

uses(DatabaseTrait::class);
beforeEach(fn() => $this->withDatabase());

it('can get a coach', function () {
    $repo = new CoachDatabaseRepository($this->db);
    try {
        $coaches = $repo->getById(1);
        expect($coaches)
            ->toBeInstanceOf(Collection::class)
            ->and($coaches->first())
            ->toBeInstanceOf(CoachEntity::class)
            ->and($coaches->first()->getName())
            ->toBeInstanceOf(Name::class)
            ->and($coaches->first()->getName()->getFirstName())
            ->toBeString()
        ;
    } catch (RepositoryException $e) {
        $this->fail((string) $e);
    }
})
    ->skip(fn() => !$this->hasDatabase(), 'No database available')
;

it('can get all active coaches', function () {
    $repo = new CoachDatabaseRepository($this->db);
    try {
        $query = $repo->createQuery();
        $query->filterActive(true);
        $coaches = $repo->getAll($query);
        expect($coaches)
            ->toBeInstanceOf(Collection::class)
        ;
        $coach = $coaches->first();
        expect($coach)
            ->toBeInstanceOf(CoachEntity::class)
        ;
    } catch (RepositoryException $e) {
        $this->fail((string) $e);
    } catch (QueryException $e) {
        $this->fail((string) $e);
    }
})
    ->skip(fn() => !$this->hasDatabase(), 'No database available')
;
