<?php
declare(strict_types=1);

use Illuminate\Support\Collection;
use Kwai\Modules\Users\Infrastructure\Repositories\UserInvitationDatabaseRepository;
use Kwai\Modules\Users\UseCases\BrowseUserInvitations;
use Kwai\Modules\Users\UseCases\BrowseUserInvitationsCommand;
use Tests\DatabaseTrait;

uses(DatabaseTrait::class);
beforeEach(fn() => $this->withDatabase());

it('can browse user invitations', function () {
    $repo = new UserInvitationDatabaseRepository($this->db);
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
    ->skip(fn() => !$this->hasDatabase(), 'No database available')
;
