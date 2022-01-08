<?php
declare(strict_types=1);

namespace Tests\Modules\Users\Infrastructure\Repositories;

use Exception;
use Illuminate\Support\Collection;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\Ability;
use Kwai\Modules\Users\Infrastructure\Repositories\AbilityDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\RuleDatabaseRepository;
use Tests\Context;

$context = Context::createContext();

it('can create an ability', function () use ($context) {
    $ruleRepo = new RuleDatabaseRepository($context->db);
    try {
        $rule = $ruleRepo->getByIds([1])->first();
    } catch (RepositoryException $e) {
        $this->fail((string) $e);
    }

    $repo = new AbilityDatabaseRepository($context->db);
    try {
        $ability = $repo->create(new Ability(
            name: 'Test',
            remark: 'Test Ability',
            rules: new Collection([$rule])
        ));
        expect($ability)
            ->toBeInstanceOf(Entity::class)
        ;
        expect($ability->domain())
            ->toBeInstanceOf(Ability::class)
        ;
    } catch (RepositoryException $e) {
        $this->fail((string) $e);
    }
    return $ability;
})
    ->skip(!Context::hasDatabase(), 'No database available')
;

it('can update an ability', function ($ability) use ($context) {
    if ($ability == null) {
        return;
    }
    $repo = new AbilityDatabaseRepository($context->db);
    try {
        $updatedAbility = new Ability(
            name: $ability->getName(),
            remark: 'Updated with a test',
            traceableTime: $ability->getTraceableTime()->markUpdated()
        );
        $repo->update(
            new Entity(
                $ability->id(),
                $updatedAbility
            )
        );
        $this->assertTrue(true);
    } catch (RepositoryException $e) {
        $this->fail((string) $e);
    }
})
    ->depends('it can create an ability')
    ->skip(!Context::hasDatabase(), 'No database available')
;

it('can get an ability', function ($ability) use ($context) {
    if ($ability == null) {
        return;
    }

    $repo = new AbilityDatabaseRepository($context->db);
    try {
        $ability = $repo->getById($ability->id());
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
    expect($ability)
        ->toBeInstanceOf(Entity::class)
    ;
    expect($ability->domain())
        ->toBeInstanceOf(Ability::class)
    ;
})
    ->depends('it can create an ability')
    ->skip(!Context::hasDatabase(), 'No database available')
;
