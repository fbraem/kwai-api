<?php

declare(strict_types=1);

use Illuminate\Support\Collection;
use Kwai\Modules\Users\Infrastructure\Repositories\RuleDatabaseRepository;
use Kwai\Modules\Users\UseCases\BrowseRules;
use Kwai\Modules\Users\UseCases\BrowseRulesCommand;
use Tests\Context;

$context = Context::createContext();

it('can browse rules', function () use ($context) {
    $command = new BrowseRulesCommand();
    try {
        [ $count, $rules ] = BrowseRules::create(
            new RuleDatabaseRepository($context->db)
        )($command);
        expect($count)
            ->toBeInt()
        ;
        expect($rules)
            ->toBeInstanceOf(Collection::class)
        ;
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
})
    ->skip(!Context::hasDatabase(), 'No database available')
;
