<?php
/**
 * Testcase for Date
 */
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use Kwai\Core\Domain\Exceptions\NotFoundException;
use Kwai\Core\Domain\Entity;

use Kwai\Modules\Users\Infrastructure\Repositories\AbilityDatabaseRepository;

require_once('Database.php');

/**
 * @group DB
 */
final class AbilityDatabaseRepositoryTest extends TestCase
{
    private $repo;

    public function setup(): void
    {
        $this->repo = new AbilityDatabaseRepository(\Database::getDatabase());
    }

    public function testGetAbilityById(): Entity
    {
        $ability = $this->repo->getById(1);
        $this->assertInstanceOf(
            Entity::class,
            $ability
        );
        return $ability;
    }
}
