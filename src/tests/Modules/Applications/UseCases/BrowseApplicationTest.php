<?php

declare(strict_types=1);

use Illuminate\Support\Collection;
use Kwai\Modules\Applications\Infrastructure\Repositories\ApplicationDatabaseRepository;
use Kwai\Modules\Applications\UseCases\BrowseApplication;
use Kwai\Modules\Applications\UseCases\BrowseApplicationCommand;
use Tests\Context;

$context = Context::createContext();

it('can browse applications', function () use ($context) {
    $repo = new ApplicationDatabaseRepository($context->db);
    $command = new BrowseApplicationCommand();
    try {
        [$count, $applications] = BrowseApplication::create($repo)($command);
        expect($count)
            ->toBeGreaterThan(0)
        ;
        expect($applications)
            ->toBeInstanceOf(Collection::class)
            ->and($applications->count())
            ->toBeGreaterThan(0)
        ;
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
})
    ->skip(!Context::hasDatabase(), 'No database available')
;
