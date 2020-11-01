<?php
/**
 * Testcase for Date
 */
declare(strict_types=1);

namespace Tests\Modules\Users\Infrastructure\Repositories;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\Exceptions\NotFoundException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\Ability;
use Kwai\Modules\Users\Infrastructure\Repositories\AbilityDatabaseRepository;
use Tests\Context;

$context = Context::createContext();

it('can create an ability', function () use ($context) {
    $repo = new AbilityDatabaseRepository($context->db);
    try {
        $ability = $repo->create(new Ability((object) [
            'name' => 'Test',
            'remark' => 'Test Ability'
        ]));
        expect($ability)
            ->toBeInstanceOf(Entity::class)
        ;
        expect($ability->domain())
            ->toBeInstanceOf(Ability::class)
        ;
        return $ability;
    } catch (RepositoryException $e) {
        $this->assertTrue(false, strval($e));
    }
    return null;
})
    ->skip(!Context::hasDatabase(), 'No database available')
;

it('can retrieve an ability', function ($ability) use ($context) {
    if ($ability == null) {
        return;
    }

    $repo = new AbilityDatabaseRepository($context->db);
    try {
        $ability = $repo->getById($ability->id());
        $this->assertInstanceOf(
            Entity::class,
            $ability
        );
        expect($ability)
            ->toBeInstanceOf(Entity::class)
        ;
        expect($ability->domain())
            ->toBeInstanceOf(Ability::class)
        ;
    } catch (NotFoundException $e) {
        $this->assertTrue(false, $e->getMessage());
    } catch (RepositoryException $e) {
        $this->assertTrue(false, $e->getMessage());
    }
})
    ->depends('it it can create an ability')
    ->skip(!Context::hasDatabase(), 'No database available')
;
