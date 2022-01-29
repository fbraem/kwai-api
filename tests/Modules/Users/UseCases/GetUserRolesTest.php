<?php

declare(strict_types=1);

use Illuminate\Support\Collection;
use Kwai\Core\Domain\ValueObjects\EmailAddress;
use Kwai\Modules\Users\Infrastructure\Repositories\UserAccountDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\UserDatabaseRepository;
use Kwai\Modules\Users\UseCases\GetUserRoles;
use Kwai\Modules\Users\UseCases\GetUserRolesCommand;
use Tests\Context;

$context = Context::createContext();

it('can get roles of a user', function () use ($context) {
    $accountRepo = new UserAccountDatabaseRepository($context->db);
    try {
        $account = $accountRepo->get(new EmailAddress('jigoro.kano@kwai.com'));
    } catch (Exception $e) {
        $this->fail((string) $e);
    }

    $command = new GetUserRolesCommand();
    $command->uuid = (string) $account->getUser()->getUuid();

    try {
        $roles = GetUserRoles::create(new UserDatabaseRepository($context->db))($command);
        expect($roles)
            ->toBeInstanceOf(Collection::class)
        ;
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
})
    ->skip(!Context::hasDatabase(), 'No database available')
;
