<?php
/**
 * Testcase for AuthenticationUser
 */
declare(strict_types=1);

namespace Tests\Modules\Users\UseCases;

use Exception;
use Kwai\Core\Domain\Entity;

use Kwai\Core\Domain\Exceptions\NotFoundException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Infrastructure\Repositories\UserAccountDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\AccessTokenDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\RefreshTokenDatabaseRepository;
use Kwai\Modules\Users\UseCases\AuthenticateUser;
use Kwai\Modules\Users\UseCases\AuthenticateUserCommand;
use Kwai\Modules\Users\Domain\RefreshToken;
use Kwai\Modules\Users\Domain\Exceptions\AuthenticationException;
use Tests\Context;

$context = Context::createContext();

it('can authenticate a user', function () use ($context) {
    $command = new AuthenticateUserCommand();
    $command->email = $_ENV['user'];
    $command->password = $_ENV['password'];

    try {
        $refreshToken = AuthenticateUser::create(
            new UserAccountDatabaseRepository($context->db),
            new AccessTokenDatabaseRepository($context->db),
            new RefreshTokenDatabaseRepository($context->db)
        )($command);
        expect($refreshToken)
            ->toBeInstanceOf(Entity::class)
        ;
        expect($refreshToken->domain())
            ->toBeInstanceOf(RefreshToken::class)
        ;
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
})
    ->skip(!Context::hasDatabase(), 'No database available')
;

it('can handle a wrong authentication', function () use ($context) {
    $command = new AuthenticateUserCommand();
    $command->email = $_ENV['user'];
    $command->password = 'invalid';

    try {
        /** @noinspection PhpUnhandledExceptionInspection */
        AuthenticateUser::create(
            new UserAccountDatabaseRepository($context->db),
            new AccessTokenDatabaseRepository($context->db),
            new RefreshTokenDatabaseRepository($context->db)
        )($command);
    } catch (NotFoundException $e) {
        $this->fail((string) $e);
    } catch (RepositoryException $e) {
        $this->fail((string) $e);
    }
})
    ->throws(AuthenticationException::class)
    ->skip(!Context::hasDatabase(), 'No database available')
;
