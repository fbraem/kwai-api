<?php

declare(strict_types=1);

use Illuminate\Support\Collection;
use Kwai\Core\Domain\Entity;
use Kwai\Modules\Club\Infrastructure\Repositories\MemberDatabaseRepository;
use Tests\DatabaseTrait;

uses(DatabaseTrait::class);

beforeEach(fn() => $this->withDatabase());

it('can get a member with a given id', function () {
    $repo = new MemberDatabaseRepository($this->db);
    try {
        $member = $repo->getById(1);
        expect($member)
            ->toBeInstanceOf(Entity::class)
        ;
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
})
    ->skip(fn() => !$this->hasDatabase(), 'No database available');


it('can get all members', function () {
    $repo = new MemberDatabaseRepository($this->db);
    try {
        $query = $repo->createQuery();
        $members = $repo->getAll($query);
        expect($members)
            ->toBeInstanceOf(Collection::class)
        ;
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
})
    ->skip(fn() => !$this->hasDatabase(), 'No database available');
