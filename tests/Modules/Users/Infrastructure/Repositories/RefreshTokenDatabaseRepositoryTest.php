<?php
declare(strict_types=1);

namespace Tests\Modules\Users\Infrastructure\Repositories;

use DateTime;
use Exception;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\EmailAddress;
use Kwai\Core\Domain\ValueObjects\Timestamp;
use Kwai\Modules\Users\Infrastructure\Repositories\AccessTokenDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\RefreshTokenDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\UserDatabaseRepository;
use Kwai\Modules\Users\Domain\AccessToken;
use Kwai\Modules\Users\Domain\RefreshToken;
use Kwai\Modules\Users\Domain\ValueObjects\TokenIdentifier;
use Tests\Context;

$context = Context::createContext();

beforeAll(function () use ($context) {
    $userRepo = new UserDatabaseRepository($context->db);
    try {
        $query = $userRepo
            ->createQuery()
            ->filterByEmail(new EmailAddress('jigoro.kano@kwai.com'))
        ;
        $users = $userRepo->getAll($query);
        if ($users->isNotEmpty()) {
            $context->user = $users->first();
        }
    } catch (QueryException $e) {
        echo $e->getMessage(), PHP_EOL;
    }
});

it('can create a refreshtoken', function () use ($context) {
    if (!isset($context->user)) {
        return null;
    }
    $repo = new RefreshTokenDatabaseRepository($context->db);
    $accessTokenRepo = new AccessTokenDatabaseRepository($context->db);
    $future = new DateTime('now +2 hours');
    $tokenIdentifier = new TokenIdentifier();
    $accessToken = new AccessToken(
        identifier: $tokenIdentifier,
        expiration: Timestamp::createFromDateTime($future),
        account: $context->user
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
            ->toBeInstanceOf(Entity::class)
        ;
        expect($entity->domain())
            ->toBeInstanceOf(RefreshToken::class)
        ;
    } catch (RepositoryException $e) {
        $this->fail((string) $e);
    }

    return $tokenIdentifier;
})
    ->skip(!Context::hasDatabase(), 'No database available')
;

it('can retrieve a refreshtoken', function (TokenIdentifier $tokenIdentifier) use ($context) {
    $repo = new RefreshTokenDatabaseRepository($context->db);
    try {
        $refreshToken = $repo->getByTokenIdentifier(
            $tokenIdentifier
        );
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
    ->depends('it can create a refreshtoken')
    ->skip(!Context::hasDatabase(), 'No database available')
;
