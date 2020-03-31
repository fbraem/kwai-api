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
use Tests\DatabaseTestCase;

class RuleDatabaseRepositoryTest extends DatabaseTestCase
{
    private RuleDatabaseRepository $repo;

    public function setup(): void
    {
        $this->repo = new RuleDatabaseRepository(self::$db);
    }

    public function testGetAll(): void
    {
        try {
            $rules = $this->repo->getAll();
            $this->assertContainsOnlyInstancesOf(
                Entity::class,
                $rules
            );
        } catch (RepositoryException $e) {
            self::assertTrue(false, $e->getMessage());
        }
    }

    public function testGetByIds(): void
    {
        try {
            $rules = $this->repo->getByIds([1, 2]);
            $this->assertContainsOnlyInstancesOf(
                Entity::class,
                $rules
            );
        } catch (RepositoryException $e) {
            self::assertTrue(false, $e->getMessage());
        }
    }

    public function testGetAllWithSubject(): void
    {
        try {
            $rules = $this->repo->getAll('all');
            $this->assertContainsOnlyInstancesOf(
                Entity::class,
                $rules
            );
        } catch (RepositoryException $e) {
            self::assertTrue(false, $e->getMessage());
        }
    }
}
