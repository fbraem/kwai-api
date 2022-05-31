<?php

declare(strict_types=1);

use Illuminate\Support\Collection;
use Kwai\Modules\Applications\Infrastructure\Repositories\ApplicationDatabaseRepository;
use Kwai\Modules\Applications\UseCases\BrowseApplication;
use Kwai\Modules\Applications\UseCases\BrowseApplicationCommand;
use Tests\DatabaseTrait;

uses(DatabaseTrait::class);
beforeEach(fn() => $this->withDatabase());

it('can browse applications', function () {
    $repo = new ApplicationDatabaseRepository($this->db);
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
    ->skip(fn() => !$this->hasDatabase(), 'No database available')
;
