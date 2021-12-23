<?php

declare(strict_types=1);

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\EmailAddress;
use Kwai\Modules\Users\Infrastructure\Repositories\UserAccountDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\UserDatabaseRepository;
use Kwai\Modules\Users\UseCases\GetUser;
use Kwai\Modules\Users\UseCases\GetUserCommand;
use Tests\Context;

$context = Context::createContext();

it('can get an user', function () use ($context) {
    $accountRepo = new UserAccountDatabaseRepository($context->db);
    try {
        $account = $accountRepo->get(new EmailAddress('jigoro.kano@kwai.com'));
    } catch (Exception $e) {
        $this->fail((string) $e);
    }

    $command = new GetUserCommand();
    $command->uuid = (string) $account->getUser()->getUuid();

    try {
        $user = GetUser::create(new UserDatabaseRepository($context->db))($command);
        expect($user)
            ->toBeInstanceOf(Entity::class)
            ->and((string) $user->getEmailAddress())
            ->toBe('jigoro.kano@kwai.com')
        ;
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
})
    ->skip(!Context::hasDatabase(), 'No database available')
;
