<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Tests\Modules\News\Infrastructure\Repositories;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Repositories\QueryException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\News\Domain\Exceptions\StoryNotFoundException;
use Kwai\Modules\News\Infrastructure\Repositories\StoryDatabaseRepository;
use Kwai\Modules\News\Repositories\StoryRepository;
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
        } catch (StoryNotFoundException $e) {
            $this->assertTrue(false, (string) $e);
        } catch (QueryException $e) {
            $this->assertTrue(false, (string) $e);
        }
    }

    public function testQuery()
    {
        $query = $this->repo->createQuery();
        $query->filterPublishDate(2020);
        try {
            $stories = $query->execute();
        } catch (QueryException $e) {
            $this->assertTrue(false, $e->getMessage());
        }
    }
}
