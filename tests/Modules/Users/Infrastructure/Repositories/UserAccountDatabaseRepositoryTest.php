<?php
declare(strict_types=1);

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\EmailAddress;
use Kwai\Core\Domain\ValueObjects\Name;
use Kwai\Core\Domain\ValueObjects\UniqueId;
use Kwai\Modules\Users\Domain\User;
use Kwai\Modules\Users\Domain\UserAccount;
use Kwai\Modules\Users\Domain\ValueObjects\Password;
use Kwai\Modules\Users\Infrastructure\Repositories\UserAccountDatabaseRepository;
use Tests\Context;

$context = Context::createContext();

it('can create a user account', function () use ($context) {
    $repo = new UserAccountDatabaseRepository($context->db);
    try {
        $userAccount = $repo->create(
            new UserAccount(
                user: new User(
                    uuid: new UniqueId(),
                    emailAddress: new EmailAddress('jigoro.kano@kwai.com'),
                    username: new Name('Jigoro', 'Kano')
                ),
                password: Password::fromString('judo')
            )
        );
        expect($userAccount)
            ->toBeInstanceOf(Entity::class)
            ->and($userAccount->domain())
            ->toBeInstanceOf(UserAccount::class)
        ;
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
})
    ->skip(!Context::hasDatabase(), 'No database available')
;

it('can get a user account', function () use ($context) {
    $repo = new UserAccountDatabaseRepository($context->db);
    try {
        $userAccount = $repo->get(new EmailAddress('jigoro.kano@kwai.com'));
        /** @noinspection PhpUndefinedMethodInspection */
        expect($userAccount)
            ->toBeInstanceOf(Entity::class)
            ->and($userAccount->domain())
            ->toBeInstanceOf(UserAccount::class)
            ->and(strval($userAccount->getUser()->getEmailAddress()))
            ->toBe('jigoro.kano@kwai.com')
        ;
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
})
    ->skip(!Context::hasDatabase(), 'No database available')
;

it('can check if a user with email exists', function () use ($context) {
    $repo = new UserAccountDatabaseRepository($context->db);
    try {
        $exist = $repo->existsWithEmail(
            new EmailAddress('jigoro.kano@kwai.com')
        );
        expect($exist)
            ->toBe(true)
        ;
        $exist = $repo->existsWithEmail(
            new EmailAddress('test@example.com')
        );
        expect($exist)
            ->toBe(false)
        ;
    } catch (Exception $e) {
        $this->assertTrue(false, (string) $e);
    }
})
    ->skip(!Context::hasDatabase(), 'No database available')
;
