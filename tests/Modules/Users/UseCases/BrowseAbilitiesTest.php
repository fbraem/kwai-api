<?php

declare(strict_types=1);

use Illuminate\Support\Collection;
use Kwai\Modules\Users\Infrastructure\Repositories\AbilityDatabaseRepository;
use Kwai\Modules\Users\UseCases\BrowseAbilities;
use Kwai\Modules\Users\UseCases\BrowseAbilitiesCommand;
use Tests\Context;

$context = Context::createContext();

it('can browse abilities', function () use ($context) {
    $command = new BrowseAbilitiesCommand();
    try {
        [ $count, $abilities ] = BrowseAbilities::create(
            new AbilityDatabaseRepository($context->db)
        )($command);
        expect($count)
            ->toBeInt()
        ;
        expect($abilities)
            ->toBeInstanceOf(Collection::class)
        ;
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
})
    ->skip(!Context::hasDatabase(), 'No database available')
;
