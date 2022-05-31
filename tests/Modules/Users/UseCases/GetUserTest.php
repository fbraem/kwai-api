<?php

declare(strict_types=1);

use Kwai\Core\Domain\ValueObjects\EmailAddress;
use Kwai\Modules\Users\Domain\UserEntity;
use Kwai\Modules\Users\Infrastructure\Repositories\UserAccountDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\UserDatabaseRepository;
use Kwai\Modules\Users\UseCases\GetUser;
use Kwai\Modules\Users\UseCases\GetUserCommand;
use Tests\DatabaseTrait;

uses(DatabaseTrait::class);
beforeEach(fn() => $this->withDatabase());

it('can get an user', function () {
    $accountRepo = new UserAccountDatabaseRepository($this->db);
    try {
        $account = $accountRepo->get(new EmailAddress('jigoro.kano@kwai.com'));
    } catch (Exception $e) {
        $this->fail((string) $e);
    }

    $command = new GetUserCommand();
    $command->uuid = (string) $account->getUser()->getUuid();

    try {
        $user = GetUser::create(new UserDatabaseRepository($this->db))($command);
        expect($user)
            ->toBeInstanceOf(UserEntity::class)
            ->and((string) $user->getEmailAddress())
            ->toBe('jigoro.kano@kwai.com')
        ;
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
})
    ->skip(fn() => !$this->hasDatabase(), 'No database available')
;
