<?php
declare(strict_types=1);

namespace Tests\Modules\Users\Infrastructure\Repositories;

use DateTime;
use Exception;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Core\Domain\ValueObjects\Timestamp;
use Kwai\Modules\Users\Domain\RefreshTokenEntity;
use Kwai\Modules\Users\Domain\UserAccount;
use Kwai\Modules\Users\Domain\UserAccountEntity;
use Kwai\Modules\Users\Domain\ValueObjects\Password;
use Kwai\Modules\Users\Infrastructure\Repositories\AccessTokenDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\RefreshTokenDatabaseRepository;
use Kwai\Modules\Users\Domain\AccessToken;
use Kwai\Modules\Users\Domain\RefreshToken;
use Kwai\Modules\Users\Domain\ValueObjects\TokenIdentifier;
use Tests\DatabaseTrait;

uses(DatabaseTrait::class);
beforeEach(fn() => $this->withDatabase());

it('can create a refreshtoken', function () {
    $repo = new RefreshTokenDatabaseRepository($this->db);
    $accessTokenRepo = new AccessTokenDatabaseRepository($this->db);
    $future = new DateTime('now +2 hours');
    $tokenIdentifier = new TokenIdentifier();
    $accessToken = new AccessToken(
        identifier: $tokenIdentifier,
        expiration: Timestamp::createFromDateTime($future),
        account: new UserAccountEntity(
            $this->withUser()->id(),
            new UserAccount(
                $this->withUser()->domain(),
                Password::fromString('test')
            )
        )
    );
    try {
        $accessTokenEntity = $accessTokenRepo->create($accessToken);
        $tokenIdentifier = new TokenIdentifier();
        $refreshToken = new RefreshToken(
            identifier: $tokenIdentifier,
            expiration: Timestamp::createFromDateTime($future),
            accessToken: $accessTokenEntity
        );
        $entity = $repo->create($refreshToken);
        expect($entity)
            ->toBeInstanceOf(RefreshTokenEntity::class)
        ;
        expect($entity->domain())
            ->toBeInstanceOf(RefreshToken::class)
        ;
    } catch (RepositoryException $e) {
        $this->fail((string) $e);
    }

    return $tokenIdentifier;
})
    ->skip(fn() => !$this->hasDatabase(), 'No database available')
;

it('can retrieve a refreshtoken', function (TokenIdentifier $tokenIdentifier) {
    $repo = new RefreshTokenDatabaseRepository($this->db);
    try {
        $refreshToken = $repo->getByTokenIdentifier(
            $tokenIdentifier
        );
        expect($refreshToken)
            ->toBeInstanceOf(RefreshTokenEntity::class)
        ;
        expect($refreshToken->domain())
            ->toBeInstanceOf(RefreshToken::class)
        ;
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
})
    ->depends('it can create a refreshtoken')
    ->skip(fn() => !$this->hasDatabase(), 'No database available')
;
