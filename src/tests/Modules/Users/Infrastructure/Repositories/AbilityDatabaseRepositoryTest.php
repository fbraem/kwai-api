<?php
/**
 * Testcase for Date
 */
declare(strict_types=1);

namespace Tests\Modules\Users\Infrastructure\Repositories;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\Exceptions\NotFoundException;
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

    public function testGetAbilityById()
    {
        try {
            $ability = $this->repo->getById(1);
            $this->assertInstanceOf(
                Entity::class,
                $ability
            );
        } catch (NotFoundException $e) {
            $this->assertTrue(false, $e->getMessage());
        }
    }
}
