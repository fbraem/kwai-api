<?php
declare(strict_types=1);

namespace Tests\Modules\Users\UseCases;

use Kwai\Core\Domain\Exceptions\NotFoundException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\Exceptions\AuthenticationException;

use Kwai\Core\Domain\Entity;

use Kwai\Modules\Users\Infrastructure\Repositories\UserDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\AccessTokenDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\RefreshTokenDatabaseRepository;
use Kwai\Modules\Users\UseCases\AuthenticateUser;
use Kwai\Modules\Users\UseCases\AuthenticateUserCommand;
use Kwai\Modules\Users\UseCases\Logout;
use Kwai\Modules\Users\UseCases\LogoutCommand;
use Kwai\Modules\Users\Domain\RefreshToken;
use Tests\Context;

$context = Context::createContext();

it('can logout', function () use ($context) {
    $command = new AuthenticateUserCommand();
    $command->email = $_ENV['user'];
    $command->password = $_ENV['password'];

    $refreshTokenRepo = new RefreshTokenDatabaseRepository($context->db);
    $accessTokenRepo = new AccessTokenDatabaseRepository($context->db);

    try {
        $refreshToken = (new AuthenticateUser(
            new UserDatabaseRepository($context->db),
            $accessTokenRepo,
            $refreshTokenRepo
        ))($command);
        expect($refreshToken)
            ->toBeInstanceOf(Entity::class)
        ;
        expect($refreshToken->domain())
            ->toBeInstanceOf(RefreshToken::class)
        ;

        $command = new LogoutCommand();
        /** @noinspection PhpUndefinedMethodInspection */
        $command->identifier = strval($refreshToken->getIdentifier());

        (new Logout(
            $refreshTokenRepo,
            $accessTokenRepo
        ))($command);
    } catch (NotFoundException $e) {
        $this->assertTrue(false, (string) $e);
    } catch (AuthenticationException $e) {
        $this->assertTrue(false, (string) $e);
    } catch (RepositoryException $e) {
        $this->assertTrue(false, (string) $e);
    }
})
    ->skip(!Context::hasDatabase(), 'No database available')
;
