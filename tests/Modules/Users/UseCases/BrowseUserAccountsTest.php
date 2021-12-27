<?php
declare(strict_types=1);

use Illuminate\Support\Collection;
use Kwai\Modules\Users\Infrastructure\Repositories\UserAccountDatabaseRepository;
use Kwai\Modules\Users\UseCases\BrowseUserAccounts;
use Kwai\Modules\Users\UseCases\BrowseUserAccountsCommand;
use Tests\Context;

$context = Context::createContext();

it('can browse user accounts', function () use ($context) {
    $command = new BrowseUserAccountsCommand();

    try {
        [$count, $accounts] = BrowseUserAccounts::create(
            new UserAccountDatabaseRepository($context->db)
        )($command);
    } catch(Exception $e) {
        $this->fail((string) $e);
    }

    expect($count)
        ->toBeInt()
    ;
    expect($accounts)
        ->toBeInstanceOf(Collection::class)
    ;
})
    ->skip(!Context::hasDatabase(), 'No database available')
;
