<?php
declare(strict_types=1);

use Illuminate\Support\Collection;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Club\Infrastructure\Repositories\TeamDatabaseRepository;
use Tests\Context;

$context = Context::createContext();

it('can get all teams', function () use ($context) {
    $repo = new TeamDatabaseRepository($context->db);

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
    ->skip(!Context::hasDatabase(), 'No database available');
