<?php
declare(strict_types=1);

use Kwai\Modules\Users\Infrastructure\Repositories\UserInvitationDatabaseRepository;
use Kwai\Modules\Users\UseCases\GetUserInvitation;
use Kwai\Modules\Users\UseCases\GetUserInvitationCommand;
use Tests\DatabaseTrait;

uses(DatabaseTrait::class);
beforeEach(fn() => $this->withDatabase());

it('can get an invitation', function () {
    $repo = new UserInvitationDatabaseRepository($this->db);
    try {
        $invitations = $repo->getAll();
    } catch (Exception $e) {
        $this->fail((string) $e);
    }

    $command = new GetUserInvitationCommand();
    $command->uuid = (string) $invitations->first()->getUuid();

    try {
        $invitation = GetUserInvitation::create($repo)->execute($command);
    } catch (Exception $e) {
        $this->fail((string) $e);
    }

    expect($invitation)
        ->toEqual($invitations->first())
    ;
})
    ->skip(fn() => !$this->hasDatabase(), 'No database available')
;
