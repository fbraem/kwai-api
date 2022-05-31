<?php
declare(strict_types=1);

namespace Tests\Modules\Users\UseCases;

use Exception;
use Kwai\Modules\Users\Domain\RefreshTokenEntity;
use Kwai\Modules\Users\Infrastructure\Repositories\UserAccountDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\AccessTokenDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\RefreshTokenDatabaseRepository;
use Kwai\Modules\Users\UseCases\AuthenticateUser;
use Kwai\Modules\Users\UseCases\AuthenticateUserCommand;
use Kwai\Modules\Users\UseCases\Logout;
use Kwai\Modules\Users\UseCases\LogoutCommand;
use Kwai\Modules\Users\Domain\RefreshToken;
use Tests\DatabaseTrait;

uses(DatabaseTrait::class);
beforeEach(fn() => $this->withDatabase());

it('can logout', function () {
    $command = new AuthenticateUserCommand();
    $command->email = $_ENV['user'];
    $command->password = $_ENV['password'];

    $refreshTokenRepo = new RefreshTokenDatabaseRepository($this->db);
    $accessTokenRepo = new AccessTokenDatabaseRepository($this->db);

    try {
        $refreshToken = AuthenticateUser::create(
            new UserAccountDatabaseRepository($this->db),
            $accessTokenRepo,
            $refreshTokenRepo
        )($command);
        expect($refreshToken)
            ->toBeInstanceOf(RefreshTokenEntity::class)
        ;
        expect($refreshToken->domain())
            ->toBeInstanceOf(RefreshToken::class)
        ;

        $command = new LogoutCommand();
        $command->identifier = strval($refreshToken->getIdentifier());

        Logout::create(
            $refreshTokenRepo,
            $accessTokenRepo
        )($command);
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
})
    ->skip(fn() => !$this->hasDatabase(), 'No database available')
;
