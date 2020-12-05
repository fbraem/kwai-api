<?php

declare(strict_types=1);

use Illuminate\Support\Collection;
use Kwai\Modules\Trainings\Infrastructure\Repositories\DefinitionDatabaseRepository;
use Kwai\Modules\Trainings\UseCases\BrowseDefinitions;
use Kwai\Modules\Trainings\UseCases\BrowseDefinitionsCommand;
use Tests\Context;

$context = Context::createContext();

it('can browse definitions', function () use ($context) {
    $repo = new DefinitionDatabaseRepository($context->db);
    $command = new BrowseDefinitionsCommand();
    try {
        [$count, $definitions] = BrowseDefinitions::create($repo)($command);
        expect($count)
            ->toBeGreaterThan(0)
        ;
        expect($definitions)
            ->toBeInstanceOf(Collection::class)
            ->and($definitions->count())
            ->toBeGreaterThan(0)
        ;
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
})
    ->skip(!Context::hasDatabase(), 'No database available')
;
