<?php
declare(strict_types=1);

use Kwai\Core\Domain\ValueObjects\EmailAddress;
use Kwai\Modules\Users\Infrastructure\Repositories\RoleDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\UserDatabaseRepository;
use Kwai\Modules\Users\UseCases\AttachRoleToUser;
use Kwai\Modules\Users\UseCases\AttachRoleToUserCommand;
use Tests\Context;

$context = Context::createContext();

it('can attach a role to a user', function () use ($context) {
    $userRepo = new UserDatabaseRepository($context->db);
    $roleRepo = new RoleDatabaseRepository($context->db);

    try {
        $users = $userRepo->getAll(
            $userRepo
                    ->createQuery()
                    ->filterByEmail(
                        new EmailAddress('jigoro.kano@kwai.com')
                    )
        );
        expect($users->count())
            ->toBeGreaterThan(0)
        ;
        $user = $users->first();
    } catch (Exception $e) {
        $this->fail((string) $e);
    }

    $command = new AttachRoleToUserCommand();
    $command->uuid = (string) $user->getUuid();
    $command->roleId = 1;
    try {
        AttachRoleToUser::create($userRepo, $roleRepo)($command);
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
})
    ->skip(!Context::hasDatabase(), 'No database available')
;
