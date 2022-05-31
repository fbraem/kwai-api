<?php

declare(strict_types=1);

use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Coaches\Domain\Exceptions\UserNotFoundException;
use Kwai\Modules\Coaches\Infrastructure\Repositories\UserDatabaseRepository;
use Tests\DatabaseTrait;

uses(DatabaseTrait::class);
beforeEach(fn() => $this->withDatabase());

it('can get a user with a given id', function () {
    $repo = new UserDatabaseRepository($this->db);
    try {
        $user = $repo->getById(1);
        expect($user)
            ->toBeInstanceOf(Entity::class)
        ;
    } catch (RepositoryException $e) {
        $this->fail((string) $e);
    } catch (QueryException $e) {
        $this->fail((string) $e);
    } catch (UserNotFoundException $e) {
        $this->fail((string) $e);
    }
})
    ->skip(fn() => !$this->hasDatabase(), 'No database available')
;
