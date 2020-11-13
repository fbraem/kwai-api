<?php
declare(strict_types=1);

namespace Tests\Modules\News\UseCases;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Repositories\ImageRepository;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Core\UseCases\Content;
use Kwai\Modules\News\Domain\Author;
use Kwai\Modules\News\Domain\Application;
use Kwai\Modules\News\Domain\Exceptions\ApplicationNotFoundException;
use Kwai\Modules\News\Domain\Exceptions\AuthorNotFoundException;
use Kwai\Modules\News\Domain\Story;
use Kwai\Modules\News\Infrastructure\Repositories\AuthorDatabaseRepository;
use Kwai\Modules\News\Infrastructure\Repositories\ApplicationDatabaseRepository;
use Kwai\Modules\News\Infrastructure\Repositories\StoryDatabaseRepository;
use Kwai\Modules\News\UseCases\CreateStory;
use Kwai\Modules\News\UseCases\CreateStoryCommand;
use Kwai\Modules\Users\Domain\ValueObjects\Username;
use Tests\Context;

$context = Context::createContext();

it('can create a story', function () use ($context) {
    $command = new CreateStoryCommand();
    try {
        $command->application = 1;
        $command->timezone = 'Europe/Brussels';
        $command->publish_date = '2020-05-01 09:00:00';
        $command->promotion = 1;

        $content = new Content();
        $content->content = 'This is a test from CreateStoryTest';
        $content->format = 'md';
        $content->locale = 'nl';
        $content->summary = 'This is a test';
        $content->title = 'Test';
        $content->author = 1;
        $command->contents = [ $content ];

        $story = (new CreateStory(
            new StoryDatabaseRepository($context->db),
            new class($context->db) extends ApplicationDatabaseRepository {
                public function getById(int $id): Entity
                {
                    return new Entity(1, new Application(
                        (object) [
                            'title' => 'Unit Test',
                            'name' => 'unit_test'
                        ]
                    ));
                }
            },
            new class($context->db) extends AuthorDatabaseRepository {
                public function getById(int $id): Entity
                {
                    return new Entity(1, new Author(
                        (object) [
                            'name' => new Username('Test', 'User')
                        ]
                    ));
                }
            },
            new class implements ImageRepository {
                public function getImages(int $id): array
                {
                    return [];
                }
                public function removeImages(int $id): void
                {
                }
            }
        ))($command);
        expect($story)
            ->toBeInstanceOf(Entity::class)
        ;
        expect($story->domain())
            ->toBeInstanceOf(Story::class)
        ;
        /** @noinspection PhpUndefinedMethodInspection */
        $contents = $story->getContents();
        expect($contents)
            ->toBeArray()
        ;
        expect($contents[0]->getTitle())
            ->toBe('Test')
        ;
        /** @noinspection PhpUndefinedMethodInspection */
        // Promotion should be active
        expect($story->getPromotion()->isActive())
            ->toBe(true)
        ;
    } catch (RepositoryException $e) {
        $this->assertTrue(false, (string) $e);
    } catch (AuthorNotFoundException $e) {
        $this->assertTrue(false, (string) $e);
    } catch (ApplicationNotFoundException $e) {
        $this->assertTrue(false, (string) $e);
    }
})
    ->skip(!Context::hasDatabase(), 'No database available')
;
