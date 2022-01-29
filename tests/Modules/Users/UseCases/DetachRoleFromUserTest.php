<?php

declare(strict_types=1);

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\EmailAddress;
use Kwai\Modules\Users\Infrastructure\Repositories\RoleDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\UserAccountDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\UserDatabaseRepository;
use Kwai\Modules\Users\UseCases\DetachRoleFromUser;
use Kwai\Modules\Users\UseCases\DetachRoleFromUserCommand;
use Tests\Context;

$context = Context::createContext();

it('can detach a role from a user', function () use ($context) {
    $userRepo = new UserAccountDatabaseRepository($context->db);
    try {
        $account = $userRepo->get(new EmailAddress('jigoro.kano@kwai.com'));
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
    $command = new DetachRoleFromUserCommand();
    $command->uuid = (string) $account->getUser()->getUuid();
    $command->roleId = 1;

    $roleCount = $account->getUser()->getRoles()->count();

    try {
        $user = DetachRoleFromUser::create(
            new UserDatabaseRepository($context->db),
            new RoleDatabaseRepository($context->db)
        )($command);
        if ( $roleCount > 0 ) {
            expect($user)
                ->toBeInstanceOf(Entity::class)
                ->and($user->getRoles()->count())
                ->toBe($roleCount - 1);
        } else {
            $this->expectNotToPerformAssertions();
        }
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
})
    ->skip(!Context::hasDatabase(), 'No database available')
;
