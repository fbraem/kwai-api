<?php
declare(strict_types=1);

use Illuminate\Support\Collection;
use Kwai\Modules\Users\Infrastructure\Repositories\UserAccountDatabaseRepository;
use Kwai\Modules\Users\UseCases\BrowseUserAccounts;
use Kwai\Modules\Users\UseCases\BrowseUserAccountsCommand;
use Tests\DatabaseTrait;

uses(DatabaseTrait::class);
beforeEach(fn() => $this->withDatabase());

it('can browse user accounts', function () {
    $command = new BrowseUserAccountsCommand();

    try {
        [$count, $accounts] = BrowseUserAccounts::create(
            new UserAccountDatabaseRepository($this->db)
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
    ->skip(fn() => !$this->hasDatabase(), 'No database available')
;
