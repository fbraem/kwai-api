<?php
declare(strict_types=1);

use Kwai\Modules\Users\Infrastructure\Repositories\UserInvitationDatabaseRepository;
use Kwai\Modules\Users\UseCases\GetUserInvitation;
use Kwai\Modules\Users\UseCases\GetUserInvitationCommand;
use Tests\Context;

$context = Context::createContext();

it('can get an invitation', function () use ($context) {
    $repo = new UserInvitationDatabaseRepository($context->db);
    try {
        $invitations = $repo->getAll();
    } catch (Exception $e) {
        $this->fail((string) $e);
    }

    $command = new GetUserInvitationCommand();
    $command->uuid = (string) $invitations->first()->getUuid();

    try {
        $invitation = GetUserInvitation::create($repo)($command);
    } catch (Exception $e) {
        $this->fail((string) $e);
    }

    expect($invitation)
        ->toEqual($invitations->first())
    ;
})
    ->skip(!Context::hasDatabase(), 'No database available')
;
