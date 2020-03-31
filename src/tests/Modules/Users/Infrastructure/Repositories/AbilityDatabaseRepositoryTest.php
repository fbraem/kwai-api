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
use Kwai\Modules\Users\Repositories\AbilityRepository;
use Tests\DatabaseTestCase;

/**
 * @group DB
 */
final class AbilityDatabaseRepositoryTest extends DatabaseTestCase
{
    private AbilityRepository $repo;

    public function setup(): void
    {
        $this->repo = new AbilityDatabaseRepository(self::$db);
    }

    public function testCreate(): Entity
    {
        try {
            $ability = $this->repo->create(new Ability((object) [
                'name' => 'Test',
                'remark' => 'Test Ability'
            ]));
            $this->assertInstanceOf(Entity::class, $ability);
            return $ability;
        } catch (RepositoryException $e) {
            $this->assertTrue(false, strval($e));
        }
        return null;
    }

    /**
     * @depends testCreate
     * @param Entity $entity
     * @throws RepositoryException
     */
    public function testGetAbilityById(Entity $entity)
    {
        try {
            $ability = $this->repo->getById($entity->id());
            $this->assertInstanceOf(
                Entity::class,
                $ability
            );
        } catch (NotFoundException $e) {
            $this->assertTrue(false, $e->getMessage());
        }
    }
}
