<?php
declare(strict_types=1);

use Kwai\Core\Domain\ValueObjects\EmailAddress;
use Kwai\Modules\Users\Infrastructure\Repositories\AbilityDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\UserDatabaseRepository;
use Kwai\Modules\Users\UseCases\AttachAbilityToUser;
use Kwai\Modules\Users\UseCases\AttachAbilityToUserCommand;
use Tests\Context;

$context = Context::createContext();

it('can attach an ability to a user', function () use ($context) {
    $userRepo = new UserDatabaseRepository($context->db);
    $abilityRepo = new AbilityDatabaseRepository($context->db);

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

    $command = new AttachAbilityToUserCommand();
    $command->uuid = (string) $user->getUuid();
    $command->abilityId = 1;
    try {
        AttachAbilityToUser::create($userRepo, $abilityRepo)($command);
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
})
    ->skip(!Context::hasDatabase(), 'No database available')
;
