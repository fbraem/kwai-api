<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Tests\Modules\News\Infrastructure\Repositories;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\DocumentFormat;
use Kwai\Core\Domain\ValueObjects\Locale;
use Kwai\Core\Domain\ValueObjects\Text;
use Kwai\Core\Domain\ValueObjects\Timestamp;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\News\Domain\Exceptions\AuthorNotFoundException;
use Kwai\Modules\News\Domain\Exceptions\CategoryNotFoundException;
use Kwai\Modules\News\Domain\Exceptions\StoryNotFoundException;
use Kwai\Modules\News\Domain\Story;
use Kwai\Modules\News\Domain\ValueObjects\Promotion;
use Kwai\Modules\News\Infrastructure\Repositories\AuthorDatabaseRepository;
use Kwai\Modules\News\Infrastructure\Repositories\CategoryDatabaseRepository;
use Kwai\Modules\News\Infrastructure\Repositories\StoryDatabaseRepository;
use Kwai\Modules\News\Repositories\AuthorRepository;
use Kwai\Modules\News\Repositories\CategoryRepository;
use Kwai\Modules\News\Repositories\StoryRepository;
use Tests\DatabaseTestCase;

class StoryDatabaseRepositoryTest extends DatabaseTestCase
{
    private StoryRepository $repo;
    private CategoryRepository $categoryRepo;
    private AuthorRepository $authorRepo;

    public function setUp(): void
    {
        $this->repo = new StoryDatabaseRepository(self::$db);
        $this->categoryRepo = new CategoryDatabaseRepository(self::$db);
        $this->authorRepo = new AuthorDatabaseRepository(self::$db);
    }

    /**
     * @return Entity<Story>|null
     */
    public function testCreate(): ?Entity
    {
        // This test will create a promoted story.
        try {
            $story = $this->repo->create(new Story(
                (object)[
                    'enabled' => true,
                    'promotion' => new Promotion(1),
                    'publishTime' => Timestamp::createNow('Europe/Brussels'),
                    'category' => $this->categoryRepo->getById(1),
                    'contents'=> [
                        new Text(
                            new Locale('nl'),
                            new DocumentFormat('md'),
                            'Unit Test',
                            'Summary for Unit Test',
                            'Content for **Unit** Test',
                            $this->authorRepo->getById(1)
                        )
                    ]
                ]
            ));
            $this->assertInstanceOf(
                Entity::class,
                $story
            );
            return $story;
        } catch (RepositoryException $e) {
            $this->assertTrue(false, (string) $e);
        } catch (CategoryNotFoundException $e) {
            $this->assertTrue(false, (string) $e);
        } catch (AuthorNotFoundException $e) {
            $this->assertTrue(false, (string) $e);
        }
        return null;
    }

    /**
     * @depends testCreate
     * @param Entity|null $lastCreated
     */
    public function testGetById(?Entity $lastCreated)
    {
        // Skip test if testCreate failed.
        if ($lastCreated == null) {
            return;
        }

        try {
            $story = $this->repo->getById($lastCreated->id());
            $this->assertInstanceOf(
                Entity::class,
                $story
            );
        } catch (StoryNotFoundException $e) {
            $this->assertTrue(false, (string) $e);
        } catch (RepositoryException $e) {
            $this->assertTrue(false, (string) $e);
        }
    }

    public function testQuery()
    {
        $query = $this->repo->createQuery();
        $query->filterPromoted();
        try {
            $stories = $query->execute();
            $this->assertGreaterThan(
                0,
                count($stories),
                'At least one story must be found'
            );
        } catch (QueryException $e) {
            $this->assertTrue(false, $e->getMessage());
        }
    }
}
