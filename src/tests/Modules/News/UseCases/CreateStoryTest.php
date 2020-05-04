<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Tests\Modules\News\UseCases;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Repositories\QueryException;
use Kwai\Modules\News\Domain\Author;
use Kwai\Modules\News\Domain\Category;
use Kwai\Modules\News\Domain\Exceptions\AuthorNotFoundException;
use Kwai\Modules\News\Domain\Exceptions\CategoryNotFoundException;
use Kwai\Modules\News\Infrastructure\Repositories\AuthorDatabaseRepository;
use Kwai\Modules\News\Infrastructure\Repositories\CategoryDatabaseRepository;
use Kwai\Modules\News\Infrastructure\Repositories\StoryDatabaseRepository;
use Kwai\Modules\News\UseCases\Content;
use Kwai\Modules\News\UseCases\CreateStory;
use Kwai\Modules\News\UseCases\CreateStoryCommand;
use Kwai\Modules\Users\Domain\ValueObjects\Username;
use Tests\DatabaseTestCase;

class CreateStoryTest extends DatabaseTestCase
{
    public function testGetArchive(): void
    {
        $command = new CreateStoryCommand();
        try {
            $command->category = '1';
            $command->timezone = 'Europe/Brussels';
            $command->publish_date = '2020-05-01 09:00:00';
            $command->promoted = 1;

            $content = new Content();
            $content->content = 'This is a test from CreateStoryTest';
            $content->format = 'md';
            $content->locale = 'nl';
            $content->summary = 'This is a test';
            $content->title = 'Test';
            $content->author = '1';
            $command->contents = [];
            $command->contents[] = $content;

            $story = (new CreateStory(
                new StoryDatabaseRepository(self::$db),
                new class(self::$db) extends CategoryDatabaseRepository {
                    public function getById(int $id): Entity
                    {
                        return new Entity(1, new Category(
                            (object) [
                                'name' => 'Unit Test'
                            ]
                        ));
                    }
                },
                new class(self::$db) extends AuthorDatabaseRepository {
                    public function getById(int $id): Entity
                    {
                        return new Entity(1, new Author(
                            (object) [
                                'name' => new Username('Test', 'User')
                            ]
                        ));
                    }
                }
            ))($command);
            $this->assertInstanceOf(
                Entity::class,
                $story
            );
        } catch (QueryException $e) {
            self::assertTrue(false, (string) $e);
        } catch (AuthorNotFoundException $e) {
            self::assertTrue(false, (string) $e);
        } catch (CategoryNotFoundException $e) {
            self::assertTrue(false, (string) $e);
        }
    }
}
