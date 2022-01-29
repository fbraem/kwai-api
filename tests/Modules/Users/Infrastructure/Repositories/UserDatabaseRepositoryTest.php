<?php
declare(strict_types=1);

namespace Tests\Modules\Users\Infrastructure\Repositories;

use Exception;
use Illuminate\Support\Collection;
use Kwai\Core\Domain\ValueObjects\EmailAddress;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\Role;
use Kwai\Modules\Users\Domain\Exceptions\UserNotFoundException;
use Kwai\Modules\Users\Domain\User;
use Kwai\Modules\Users\Infrastructure\Repositories\UserDatabaseRepository;
use Tests\Context;

$context = Context::createContext();

it('can get a user with email', function () use ($context) {
    $repo = new UserDatabaseRepository($context->db);
    try {
        $query = $repo
            ->createQuery()
            ->filterByEmail(new EmailAddress('jigoro.kano@kwai.com'));
        $users = $repo->getAll($query);
        expect($users)
            ->toBeInstanceOf(Collection::class)
            ->and($users->first())
            ->toBeInstanceOf(Entity::class)
        ;
        return $users->first();
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
})
    ->skip(!Context::hasDatabase(), 'No database available')
;

it('can get a user with the given id', function ($user) use ($context) {
    $repo = new UserDatabaseRepository($context->db);
    try {
        $entity = $repo->getById($user->id());
        expect($entity)
            ->toBeInstanceOf(Entity::class)
        ;
        expect($entity->domain())
            ->toBeInstanceOf(User::class)
        ;
        /** @noinspection PhpUndefinedMethodInspection */
        expect($entity->getRoles())
            ->toBeInstanceOf(Collection::class)
        ;
        /* @noinspection PhpUndefinedMethodInspection */
        return $entity->getUuid();
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
})
    ->depends('it can get a user with email')
    ->skip(!Context::hasDatabase(), 'No database available')
;

it('can get a user with the given uuid', function ($uuid) use ($context) {
    $repo = new UserDatabaseRepository($context->db);
    try {
        $user = $repo->getByUniqueId($uuid);
        expect($user)
            ->toBeInstanceOf(Entity::class)
        ;
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
})
    ->depends('it can get a user with the given id')
    ->skip(!Context::hasDatabase(), 'No database available')
;

it('throws a not found exception when user does not exist', function () use ($context) {
    $repo = new UserDatabaseRepository($context->db);
    try {
        $repo->getById(10000);
    } catch (RepositoryException $e) {
        $this->fail((string) $e);
    }
})
    ->skip(!Context::hasDatabase(), 'No database available')
    ->throws(UserNotFoundException::class)
;

it('can add a role', function ($user) use ($context) {
    $repo = new UserDatabaseRepository($context->db);

    $roles = new Collection([
        new Entity(
            1,
            new Role(name: 'Admin')
        )
    ]);

    try {
        $repo->insertRoles($user, $roles);
    } catch (RepositoryException $e) {
        $this->fail((string) $e);
    }

    try {
        $user = $repo->getById($user->id());
    } catch (RepositoryException $e) {
        $this->fail((string) $e);
    } catch (UserNotFoundException $e) {
        $this->fail((string) $e);
    }
    expect($user->getRoles()->toArray())
        ->toHaveCount(1)
    ;
})
    ->depends('it can get a user with email')
    ->skip(!Context::hasDatabase(), 'No database available')
;

it('can remove a role', function ($user) use ($context) {
    $repo = new UserDatabaseRepository($context->db);

    $roles = new Collection([
        new Entity(
            1,
            new Role(name: 'Admin')
        )
    ]);

    try {
        $repo->deleteRoles($user, $roles);
    } catch (RepositoryException $e) {
        $this->fail((string) $e);
    }

    try {
        $user = $repo->getById($user->id());
    } catch (RepositoryException $e) {
        $this->fail((string) $e);
    } catch (UserNotFoundException $e) {
        $this->fail((string) $e);
    }
    expect($user->getRoles()->toArray())
        ->toHaveCount(0)
    ;
})
    ->depends('it can get a user with email')
    ->skip(!Context::hasDatabase(), 'No database available')
;
