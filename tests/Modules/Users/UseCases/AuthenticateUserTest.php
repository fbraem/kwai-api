<?php
/**
 * Testcase for AuthenticationUser
 */
declare(strict_types=1);

namespace Tests\Modules\Users\UseCases;

use Exception;
use Kwai\Core\Domain\Exceptions\NotFoundException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\Exceptions\UserAccountNotFoundException;
use Kwai\Modules\Users\Domain\RefreshTokenEntity;
use Kwai\Modules\Users\Infrastructure\Repositories\UserAccountDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\AccessTokenDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\RefreshTokenDatabaseRepository;
use Kwai\Modules\Users\UseCases\AuthenticateUser;
use Kwai\Modules\Users\UseCases\AuthenticateUserCommand;
use Kwai\Modules\Users\Domain\Exceptions\AuthenticationException;
use Tests\DatabaseTrait;

uses(DatabaseTrait::class);
beforeEach(fn() => $this->withDatabase());

it('can authenticate a user', function () {
    $command = new AuthenticateUserCommand();
    $command->email = $_ENV['user'];
    $command->password = $_ENV['password'];

    try {
        $refreshToken = AuthenticateUser::create(
            new UserAccountDatabaseRepository($this->db),
            new AccessTokenDatabaseRepository($this->db),
            new RefreshTokenDatabaseRepository($this->db)
        )($command);
        expect($refreshToken)
            ->toBeInstanceOf(RefreshTokenEntity::class)
        ;
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
})
    ->skip(fn() => !$this->hasDatabase(), 'No database available')
;

it('can handle a wrong authentication', function () {
    $command = new AuthenticateUserCommand();
    $command->email = $_ENV['user'];
    $command->password = 'invalid';

    try {
        AuthenticateUser::create(
            new UserAccountDatabaseRepository($this->db),
            new AccessTokenDatabaseRepository($this->db),
            new RefreshTokenDatabaseRepository($this->db)
        )($command);
    } catch (NotFoundException $e) {
        $this->fail((string) $e);
    } catch (RepositoryException $e) {
        $this->fail((string) $e);
    } catch (UserAccountNotFoundException $e) {
        $this->fail((string) $e);
    }
})
    ->throws(AuthenticationException::class)
    ->skip(fn() => !$this->hasDatabase(), 'No database available')
;
