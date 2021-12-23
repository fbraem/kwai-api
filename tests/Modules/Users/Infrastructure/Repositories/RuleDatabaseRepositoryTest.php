<?php

declare(strict_types=1);

namespace Tests\Modules\Users\Infrastructure\Repositories;

use Exception;
use Illuminate\Support\Collection;
use Kwai\Core\Domain\Entity;
use Kwai\Modules\Users\Infrastructure\Repositories\RuleDatabaseRepository;
use Tests\Context;

$context = Context::createContext();

it('can retrieve all rules', function () use ($context) {
    $repo = new RuleDatabaseRepository($context->db);
    try {
        $rules = $repo->getAll();
        expect($rules)
            ->toBeInstanceOf(Collection::class)
            ->and($rules->first())
            ->toBeInstanceOf(Entity::class)
        ;
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
})
    ->skip(!Context::hasDatabase(), 'No database available')
;

it('can retrieve rules with ids', function () use ($context) {
    $repo = new RuleDatabaseRepository($context->db);
    try {
        $rules = $repo->getByIds([1, 2]);
        expect($rules)
            ->toBeInstanceOf(Collection::class)
            ->and($rules->first())
            ->toBeInstanceOf(Entity::class)
        ;
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
})
    ->skip(!Context::hasDatabase(), 'No database available')
;

it('can retrieve rules for subject', function () use ($context) {
    $repo = new RuleDatabaseRepository($context->db);
    try {
        $query = $repo->createQuery();
        $query->filterBySubject('all');
        $rules = $repo->getAll($query);
        expect($rules)
            ->toBeInstanceOf(Collection::class)
            ->and($rules->first())
            ->toBeInstanceOf(Entity::class)
        ;
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
})
    ->skip(!Context::hasDatabase(), 'No database available')
;
