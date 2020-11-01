<?php
/**
 * Testcase for Date
 */
declare(strict_types=1);

namespace Tests\Modules\Users\Infrastructure\Repositories;

use DateTime;
use Kwai\Core\Domain\Exceptions\NotFoundException;
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
        $context->user = $userRepo->getAccount(
            new EmailAddress($_ENV['user'])
        );
    } catch (NotFoundException $e) {
        echo $e->getMessage(), PHP_EOL;
    } catch (RepositoryException $e) {
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
    $accessToken = new AccessToken((object) [
        'identifier' => $tokenIdentifier,
        'expiration' => Timestamp::createFromDateTime($future),
        'account' => $context->user
    ]);
    try {
        $accessTokenEntity = $accessTokenRepo->create($accessToken);
        $tokenIdentifier = new TokenIdentifier();
        $refreshToken = new RefreshToken((object) [
            'identifier' => $tokenIdentifier,
            'expiration' => Timestamp::createFromDateTime($future),
            'accessToken' => $accessTokenEntity
        ]);
        $entity = $repo->create($refreshToken);
        expect($entity)
            ->toBeInstanceOf(Entity::class)
        ;
        expect($entity->domain())
            ->toBeInstanceOf(RefreshToken::class)
        ;
    } catch (RepositoryException $e) {
        $this->assertTrue(false, (string) $e);
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
    } catch (NotFoundException $e) {
        $this->assertTrue(false, (string) $e);
    } catch (RepositoryException $e) {
        $this->assertTrue(false, (string) $e);
    }
})
    ->depends('it it can create a refreshtoken')
    ->skip(!Context::hasDatabase(), 'No database available')
;
