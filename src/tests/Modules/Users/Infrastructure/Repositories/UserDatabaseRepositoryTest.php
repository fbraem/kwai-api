<?php
declare(strict_types=1);

namespace Tests\Modules\Users\Infrastructure\Repositories;

use Kwai\Core\Domain\ValueObjects\EmailAddress;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\Exceptions\NotFoundException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\User;
use Kwai\Modules\Users\Infrastructure\Repositories\AccessTokenDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\UserDatabaseRepository;
use Tests\Context;

$context = Context::createContext();

it('can get a user with email', function () use ($context) {
    $repo = new UserDatabaseRepository($context->db);
    try {
        $user = $repo->getByEmail(new EmailAddress('test@kwai.com'));
        expect($user)
            ->toBeInstanceOf(Entity::class)
        ;
        expect($user->domain())
            ->toBeInstanceOf(User::class)
        ;
        return $user;
    } catch (NotFoundException $e) {
        $this->assertTrue(false, (string) $e);
    } catch (RepositoryException $e) {
        $this->assertTrue(false, (string) $e);
    }
    return null;
})
    ->skip(!Context::hasDatabase(), 'No database available')
;

it('can check if a user with email exists', function () use ($context) {
    $repo = new UserDatabaseRepository($context->db);
    try {
        $exist = $repo->existsWithEmail(
            new EmailAddress('test@kwai.com')
        );
        expect($exist)
            ->toBe(true)
        ;
        $exist = $repo->existsWithEmail(
            new EmailAddress('test@example.com')
        );
        expect($exist)
            ->toBe(false)
        ;
    } catch (RepositoryException $e) {
        $this->assertTrue(false, (string) $e);
    }
})
    ->skip(!Context::hasDatabase(), 'No database available')
;

it('can get a user with the given id', function ($user) use ($context) {
    $repo = new UserDatabaseRepository($context->db);
    try {
        $entity = $repo->getById($user->id());
        $this->assertInstanceOf(
            Entity::class,
            $entity
        );
        expect($entity)
            ->toBeInstanceOf(Entity::class)
        ;
        expect($entity->domain())
            ->toBeInstanceOf(User::class)
        ;
        /* @noinspection PhpUndefinedMethodInspection */
        return $entity->getUuid();
    } catch (NotFoundException $e) {
        $this->assertTrue(false, (string) $e);
    } catch (RepositoryException $e) {
        $this->assertTrue(false, (string) $e);
    }
    return null;
})
    ->depends('it it can get a user with email')
    ->skip(!Context::hasDatabase(), 'No database available')
;

it('can get a user with the given uuid', function ($uuid) use ($context) {
    $repo = new UserDatabaseRepository($context->db);
    try {
        $user = $repo->getByUuid($uuid);
        expect($user)
            ->toBeInstanceOf(Entity::class)
        ;
        expect($user->domain())
            ->toBeInstanceOf(User::class)
        ;
    } catch (NotFoundException $e) {
        $this->assertTrue(false, (string) $e);
    } catch (RepositoryException $e) {
        $this->assertTrue(false, (string) $e);
    }
})
    ->depends('it it can get a user with the given id')
    ->skip(!Context::hasDatabase(), 'No database available')
;

it('throws a not found exception when user doesnot exist', function () use ($context) {
    $repo = new UserDatabaseRepository($context->db);
    try {
        /** @noinspection PhpUnhandledExceptionInspection */
        $repo->getById(10000);
    } catch (RepositoryException $e) {
        $this->assertTrue(false, (string) $e);
    }
})
    ->skip(!Context::hasDatabase(), 'No database available')
    ->throws(NotFoundException::class)
;

it('can get a user with an accesstoken', function ($user) use ($context) {
    $repo = new UserDatabaseRepository($context->db);
    $accessTokenRepo = new AccessTokenDatabaseRepository($context->db);
    try {
        $accessTokens = $accessTokenRepo->getTokensForUser($user);
        if (count($accessTokens) > 0) {
            try {
                /** @noinspection PhpUndefinedMethodInspection */
                $user = $repo->getByAccessToken(
                    $accessTokens[0]->getIdentifier()
                );
                expect($user)
                    ->toBeInstanceOf(Entity::class)
                ;
                expect($user->domain())
                    ->toBeInstanceOf(User::class)
                ;
            } catch (NotFoundException $e) {
                $this->assertTrue(false, (string)$e);
            }
        }
    } catch (RepositoryException $e) {
        $this->assertTrue(false, (string)$e);
    }
})
    ->depends('it it can get a user with email')
    ->skip(!Context::hasDatabase(), 'No database available')
;
