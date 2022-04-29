<?php

declare(strict_types=1);

namespace Tests\Modules\Users\Infrastructure\Repositories;

use Exception;
use Illuminate\Support\Collection;
use Kwai\Modules\Users\Domain\RuleEntity;
use Kwai\Modules\Users\Infrastructure\Repositories\RuleDatabaseRepository;
use Tests\Context;
use Tests\DatabaseTrait;

uses(DatabaseTrait::class);
beforeEach()->withDatabase();

it('can retrieve all rules', function () {
    $repo = new RuleDatabaseRepository($this->db);
    try {
        $rules = $repo->getAll();
        expect($rules)
            ->toBeInstanceOf(Collection::class)
            ->and($rules->first())
            ->toBeInstanceOf(RuleEntity::class)
        ;
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
})
    ->skip(fn () => !$this->hasDatabase(), 'No database available')
;

it('can retrieve rules with ids', function () {
    $repo = new RuleDatabaseRepository($this->db);
    try {
        $rules = $repo->getByIds([1, 2]);
        expect($rules)
            ->toBeInstanceOf(Collection::class)
            ->and($rules->first())
            ->toBeInstanceOf(RuleEntity::class)
        ;
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
})
    ->skip(fn () => !$this->hasDatabase(), 'No database available')
;

it('can retrieve rules for subject', function () {
    $repo = new RuleDatabaseRepository($this->db);
    try {
        $query = $repo->createQuery();
        $query->filterBySubject('all');
        $rules = $repo->getAll($query);
        expect($rules)
            ->toBeInstanceOf(Collection::class)
            ->and($rules->first())
            ->toBeInstanceOf(RuleEntity::class)
        ;
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
})
    ->skip(fn () => !$this->hasDatabase(), 'No database available')
;
