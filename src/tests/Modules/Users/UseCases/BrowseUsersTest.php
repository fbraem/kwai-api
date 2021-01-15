<?php

declare(strict_types=1);

use Illuminate\Support\Collection;
use Kwai\Modules\Users\Infrastructure\Repositories\UserDatabaseRepository;
use Kwai\Modules\Users\UseCases\BrowseUsers;
use Kwai\Modules\Users\UseCases\BrowseUsersCommand;
use Tests\Context;

$context = Context::createContext();

it('can browse users', function () use ($context) {
    $repo = new UserDatabaseRepository($context->db);
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
    ->skip(!Context::hasDatabase(), 'No database available')
;
