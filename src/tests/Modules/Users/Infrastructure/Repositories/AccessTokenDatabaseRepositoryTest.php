<?php
declare(strict_types=1);

namespace Tests\Modules\Users\Infrastructure\Repositories;

use DateTime;
use Kwai\Core\Domain\ValueObjects\EmailAddress;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\Exceptions\NotFoundException;
use Kwai\Core\Domain\ValueObjects\Timestamp;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\AccessToken;
use Kwai\Modules\Users\Domain\ValueObjects\TokenIdentifier;
use Kwai\Modules\Users\Infrastructure\Repositories\AccessTokenDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\UserDatabaseRepository;
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

it('can create an accesstoken', function () use ($context) {
    if (!isset($context->user)) {
        return null;
    }
    $future = new DateTime('now +2 hours');
    $tokenIdentifier = new TokenIdentifier();
    $accessToken = new AccessToken((object) [
        'identifier' => $tokenIdentifier,
        'expiration' => Timestamp::createFromDateTime($future),
        'account' => $context->user
    ]);
    $repo = new AccessTokenDatabaseRepository($context->db);
    try {
        $entity = $repo->create($accessToken);
        expect($entity)
            ->toBeInstanceOf(Entity::class)
        ;
    } catch (RepositoryException $e) {
        $this->assertTrue(false, (string) $e);
    }
    return $tokenIdentifier;
})
    ->skip(!Context::hasDatabase(), 'No database available')
;

it('can get an accesstoken for a user', function () use ($context) {
    if (!isset($context->user)) {
        return null;
    }
    $repo = new AccessTokenDatabaseRepository($context->db);
    try {
        $accessTokens = $repo->getTokensForUser($context->user);
        expect($accessTokens)
            ->toBeArray()
        ;
        $this->assertContainsOnlyInstancesOf(
            Entity::class,
            $accessTokens
        );
    } catch (RepositoryException $e) {
        $this->assertTrue(false, (string) $e);
    }
})
    ->skip(!Context::hasDatabase(), 'No database available')
;

it('can get an accesstoken with an identifier', function ($tokenIdentifier) use ($context) {
    $repo = new AccessTokenDatabaseRepository($context->db);
    try {
        $accessToken = $repo->getByTokenIdentifier($tokenIdentifier);
        expect($accessToken)
            ->toBeInstanceOf(Entity::class)
        ;
        expect($accessToken->domain())
            ->toBeInstanceOf(AccessToken::class)
        ;
    } catch (NotFoundException $e) {
        $this->assertTrue(false, (string) $e);
    } catch (RepositoryException $e) {
        $this->assertTrue(false, (string) $e);
    }
})
    ->depends('it can create an accesstoken')
    ->skip(!Context::hasDatabase(), 'No database available')
;
