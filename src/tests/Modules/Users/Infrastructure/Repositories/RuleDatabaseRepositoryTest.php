<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Tests\Modules\Users\Infrastructure\Repositories;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Infrastructure\Repositories\RuleDatabaseRepository;
use Tests\Context;

$context = Context::createContext();

it('can retrieve all rules', function () use ($context) {
    $repo = new RuleDatabaseRepository($context->db);
    try {
        $rules = $repo->getAll();
        expect($rules)
            ->toBeArray()
        ;
        $this->assertContainsOnlyInstancesOf(
            Entity::class,
            $rules
        );
    } catch (RepositoryException $e) {
        $this->assertTrue(false, (string) $e);
    }
})
    ->skip(!Context::hasDatabase(), 'No database available')
;

it('can retrieve rules with ids', function () use ($context) {
    $repo = new RuleDatabaseRepository($context->db);
    try {
        $rules = $repo->getByIds([1, 2]);
        $this->assertContainsOnlyInstancesOf(
            Entity::class,
            $rules
        );
    } catch (RepositoryException $e) {
        $this->assertTrue(false, (string) $e);
    }
})
    ->skip(!Context::hasDatabase(), 'No database available')
;

it('can retrieve rules for subject', function () use ($context) {
    $repo = new RuleDatabaseRepository($context->db);
    try {
        $rules = $repo->getAll('all');
        $this->assertContainsOnlyInstancesOf(
            Entity::class,
            $rules
        );
    } catch (RepositoryException $e) {
        $this->assertTrue(false, $e->getMessage());
    }
})
    ->skip(!Context::hasDatabase(), 'No database available')
;
