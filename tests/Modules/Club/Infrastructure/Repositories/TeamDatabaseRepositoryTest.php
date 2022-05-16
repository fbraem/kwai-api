<?php
declare(strict_types=1);

use Illuminate\Support\Collection;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Club\Infrastructure\Repositories\TeamDatabaseRepository;
use Tests\DatabaseTrait;

uses(DatabaseTrait::class);

beforeEach(fn() => $this->withDatabase());

it('can get all teams', function () {
    $repo = new TeamDatabaseRepository($this->db);

    try {
        $query = $repo->createQuery();
        $teams = $repo->getAll($query);
        expect($teams)
            ->toBeInstanceOf(Collection::class)
        ;
    } catch (RepositoryException $e) {
        $this->fail((string) $e);
    }
})
    ->skip(fn() => !$this->hasDatabase(), 'No database available')
;
