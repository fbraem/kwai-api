<?php

declare(strict_types=1);

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\EmailAddress;
use Kwai\Modules\Users\Infrastructure\Repositories\AbilityDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\UserAccountDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\UserDatabaseRepository;
use Kwai\Modules\Users\UseCases\DetachAbilityFromUser;
use Kwai\Modules\Users\UseCases\DetachAbilityFromUserCommand;
use Tests\Context;

$context = Context::createContext();

it('can detach an ability from a user', function () use ($context) {
    $userRepo = new UserAccountDatabaseRepository($context->db);
    try {
        $account = $userRepo->get(new EmailAddress('jigoro.kano@kwai.com'));
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
    $command = new DetachAbilityFromUserCommand();
    $command->uuid = (string) $account->getUser()->getUuid();
    $command->abilityId = 1;

    $abilityCount = $account->getUser()->getAbilities()->count();

    try {
        $user = DetachAbilityFromUser::create(
            new UserDatabaseRepository($context->db),
            new AbilityDatabaseRepository($context->db)
        )($command);
        expect($user)
            ->toBeInstanceOf(Entity::class)
            ->and($user->getAbilities()->count())
            ->toBe($abilityCount - 1)
        ;
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
})
    ->skip(!Context::hasDatabase(), 'No database available')
;
