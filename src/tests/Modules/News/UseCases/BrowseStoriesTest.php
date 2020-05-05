<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Tests\Modules\News\UseCases;

use Kwai\Core\Infrastructure\Repositories\ImageRepository;
use Kwai\Core\Infrastructure\Repositories\QueryException;
use Kwai\Modules\News\Infrastructure\Repositories\StoryDatabaseRepository;
use Kwai\Modules\News\UseCases\BrowseStories;
use Kwai\Modules\News\UseCases\BrowseStoriesCommand;
use Tests\DatabaseTestCase;

class BrowseStoriesTest extends DatabaseTestCase
{
    public function testBrowse(): void
    {
        $command = new BrowseStoriesCommand();

        try {
            $stories = (new BrowseStories(
                new StoryDatabaseRepository(self::$db),
                new class implements ImageRepository {
                    public function getImages(int $id): array
                    {
                        return [];
                    }
                }
            ))($command);
            self::assertGreaterThan(0, $stories->getCount(), 'No stories found!');
        } catch (QueryException $e) {
            self::assertTrue(false, (string) $e);
        }
    }
}