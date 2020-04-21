<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Tests\Modules\News\Infrastructure\Repositories;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\News\Domain\Exceptions\StoryNotFoundException;
use Kwai\Modules\News\Infrastructure\Repositories\StoryDatabaseRepository;
use Kwai\Modules\News\Repositories\StoryRepository;
use PHPUnit\Framework\TestCase;
use Tests\DatabaseTestCase;

class StoryDatabaseRepositoryTest extends DatabaseTestCase
{
    private StoryRepository $repo;

    public function setUp(): void
    {
        $this->repo = new StoryDatabaseRepository(self::$db);
    }

    public function testGetById()
    {
        try {
            $story = $this->repo->getById(1);
            $this->assertInstanceOf(
                Entity::class,
                $story
            );
        } catch (RepositoryException $e) {
            $this->assertTrue(false, $e->getMessage());
        } catch (StoryNotFoundException $e) {
            $this->assertTrue(false, $e->getMessage());
        }
    }
}
