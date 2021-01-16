<?php
declare(strict_types=1);

namespace Tests\Modules\Users\UseCases;

use Exception;
use Kwai\Core\Domain\Entity;
use Kwai\Modules\Users\Domain\RefreshToken;
use Kwai\Modules\Users\Infrastructure\Repositories\AccessTokenDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\RefreshTokenDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\UserAccountDatabaseRepository;
use Kwai\Modules\Users\UseCases\AuthenticateUser;
use Kwai\Modules\Users\UseCases\AuthenticateUserCommand;
use Kwai\Modules\Users\UseCases\CreateRefreshToken;
use Kwai\Modules\Users\UseCases\CreateRefreshTokenCommand;
use Tests\Context;

$context = Context::createContext();

it('can create a refreshtoken', function () use ($context) {
    $command = new AuthenticateUserCommand();
    $command->email = $_ENV['user'];
    $command->password = $_ENV['password'];

    $refreshTokenRepo = new RefreshTokenDatabaseRepository($context->db);
    $accessTokenRepo = new AccessTokenDatabaseRepository($context->db);

    try {
        $refreshToken = (new AuthenticateUser(
            new UserAccountDatabaseRepository($context->db),
            $accessTokenRepo,
            $refreshTokenRepo
        ))($command);

        $command = new CreateRefreshTokenCommand();
        /** @noinspection PhpUndefinedMethodInspection */
        $command->identifier = strval($refreshToken->getIdentifier());

        $refreshToken = (new CreateRefreshToken(
            $refreshTokenRepo,
            $accessTokenRepo
        ))($command);

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
