<?php
declare(strict_types=1);

use Illuminate\Support\Collection;
use Kwai\Modules\Users\Infrastructure\Repositories\UserInvitationDatabaseRepository;
use Kwai\Modules\Users\UseCases\BrowseUserInvitations;
use Kwai\Modules\Users\UseCases\BrowseUserInvitationsCommand;
use Tests\Context;

$context = Context::createContext();

it('can browse user invitations', function () use ($context) {
    $repo = new UserInvitationDatabaseRepository($context->db);
    $command = new BrowseUserInvitationsCommand();
    $command->limit = 10;
    try {
        [$count, $invitations] = BrowseUserInvitations::create($repo)($command);
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
    expect($count)
        ->toBeInt()
        ->toBeGreaterThan(0)
    ;
    expect($invitations)
        ->toBeInstanceOf(Collection::class)
        ->and($invitations->count())
        ->toBeGreaterThan(0)
    ;
})
    ->skip(!Context::hasDatabase(), 'No database available')
;
