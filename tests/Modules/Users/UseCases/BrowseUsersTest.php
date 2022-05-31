<?php
declare(strict_types=1);

use Illuminate\Support\Collection;
use Kwai\Modules\Users\Infrastructure\Repositories\UserDatabaseRepository;
use Kwai\Modules\Users\UseCases\BrowseUsers;
use Kwai\Modules\Users\UseCases\BrowseUsersCommand;
use Tests\DatabaseTrait;

uses(DatabaseTrait::class);
beforeEach(fn() => $this->withDatabase());

it('can browse users', function () {
    $repo = new UserDatabaseRepository($this->db);
    $command = new BrowseUsersCommand();

    try {
        [$count, $users] = BrowseUsers::create($repo)($command);
        expect($count)
            ->toBeInt()
            ->toBeGreaterThan(0)
        ;
        expect($users)
            ->toBeInstanceOf(Collection::class)
            ->and($users->count())
            ->toBeGreaterThan(0)
        ;
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
})
    ->skip(fn() => !$this->hasDatabase(), 'No database available')
;
