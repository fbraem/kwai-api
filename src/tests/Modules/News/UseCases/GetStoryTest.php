<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Tests\Modules\News\UseCases;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Repositories\ImageRepository;
use Kwai\Core\Infrastructure\Repositories\QueryException;
use Kwai\Modules\News\Domain\Exceptions\StoryNotFoundException;
use Kwai\Modules\News\Infrastructure\Repositories\StoryDatabaseRepository;
use Kwai\Modules\News\UseCases\GetStory;
use Kwai\Modules\News\UseCases\GetStoryCommand;
use Tests\DatabaseTestCase;

class GetStoryTest extends DatabaseTestCase
{
    public function testGet()
    {
        $command = new GetStoryCommand();
        $command->id = 1;

        try {
            $story = (new GetStory(
                new StoryDatabaseRepository(self::$db),
                new class implements ImageRepository {
                    public function getImages(int $id): array
                    {
                        return [];
                    }
                }
            ))($command);
            $this->assertInstanceOf(
                Entity::class,
                $story
            );
        } catch (QueryException $e) {
            self::assertTrue(false, (string) $e);
        } catch (StoryNotFoundException $e) {
            self::assertTrue(false, (string) $e);
        }
    }
}
