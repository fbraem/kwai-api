<?php
declare(strict_types=1);

namespace Tests\Modules\Users\Infrastructure\Repositories;

use Exception;
use Illuminate\Support\Collection;
use Kwai\Core\Infrastructure\Dependencies\Settings;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\Exceptions\UserNotFoundException;
use Kwai\Modules\Users\Domain\User;
use Kwai\Modules\Users\Domain\UserEntity;
use Kwai\Modules\Users\Infrastructure\Repositories\UserDatabaseRepository;
use Tests\DatabaseTrait;

uses(DatabaseTrait::class);
beforeEach(fn() => $this->withDatabase());

it('can get a user with email', function () {
    $settings = depends('kwai.settings', Settings::class);
    $repo = new UserDatabaseRepository($this->db);
    try {
        $query = $repo
            ->createQuery()
            ->filterByEmail($settings->getWebsiteConfiguration()->getAddress()->getEmail());
        $users = $repo->getAll($query);
        expect($users)
            ->toBeInstanceOf(Collection::class)
            ->and($users->first())
            ->toBeInstanceOf(UserEntity::class)
        ;
        return $users->first();
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
})
    ->skip(fn() => !$this->hasDatabase(), 'No database available')
;

it('can get a user with the given id', function ($user) {
    $repo = new UserDatabaseRepository($this->db);
    try {
        $entity = $repo->getById($user->id());
        expect($entity)
            ->toBeInstanceOf(UserEntity::class)
        ;
        expect($entity->domain())
            ->toBeInstanceOf(User::class)
        ;
        return $entity->getUuid();
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
})
    ->depends('it can get a user with email')
    ->skip(fn() => !$this->hasDatabase(), 'No database available')
;

it('can get a user with the given uuid', function ($uuid) {
    $repo = new UserDatabaseRepository($this->db);
    try {
        $user = $repo->getByUniqueId($uuid);
        expect($user)
            ->toBeInstanceOf(UserEntity::class)
        ;
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
})
    ->depends('it can get a user with the given id')
    ->skip(fn() => !$this->hasDatabase(), 'No database available')
;

it('throws a not found exception when user does not exist', function () {
    $repo = new UserDatabaseRepository($this->db);
    try {
        $repo->getById(10000);
    } catch (RepositoryException $e) {
        $this->fail((string) $e);
    }
})
    ->skip(fn() => !$this->hasDatabase(), 'No database available')
    ->throws(UserNotFoundException::class)
;
