<?php

declare(strict_types=1);

use Illuminate\Support\Collection;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Club\Domain\Exceptions\MemberNotFoundException;
use Kwai\Modules\Club\Infrastructure\Repositories\MemberDatabaseRepository;
use Tests\Context;

$context = Context::createContext();

it('can get a member with a given id', function () use ($context) {
    $repo = new MemberDatabaseRepository($context->db);
    try {
        $member = $repo->getById(1);
        expect($member)
            ->toBeInstanceOf(Entity::class)
        ;
    } catch (RepositoryException $e) {
        $this->fail((string) $e);
    } catch (QueryException $e) {
        $this->fail((string) $e);
    } catch (MemberNotFoundException $e) {
        $this->fail((string) $e);
    }
})
    ->skip(!Context::hasDatabase(), 'No database available');


it('can get all members', function () use ($context) {
    $repo = new MemberDatabaseRepository($context->db);
    try {
        $query = $repo->createQuery();
        $members = $repo->getAll($query);
        expect($members)
            ->toBeInstanceOf(Collection::class)
        ;
    } catch (RepositoryException $e) {
        $this->fail((string) $e);
    } catch (QueryException $e) {
        $this->fail((string) $e);
    } catch (MemberNotFoundException $e) {
        $this->fail((string) $e);
    }
})
    ->skip(!Context::hasDatabase(), 'No database available');
