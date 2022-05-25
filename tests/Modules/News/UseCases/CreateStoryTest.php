<?php
declare(strict_types=1);

namespace Tests\Modules\News\UseCases;

use Illuminate\Support\Collection;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\Creator;
use Kwai\Core\Domain\ValueObjects\DocumentFormat;
use Kwai\Core\Domain\ValueObjects\Locale;
use Kwai\Core\Domain\ValueObjects\Name;
use Kwai\Core\Infrastructure\Repositories\ImageRepository;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Core\UseCases\Content;
use Kwai\Modules\News\Domain\Application;
use Kwai\Modules\News\Domain\Exceptions\ApplicationNotFoundException;
use Kwai\Modules\News\Domain\Exceptions\AuthorNotFoundException;
use Kwai\Modules\News\Domain\Story;
use Kwai\Modules\News\Infrastructure\Repositories\ApplicationDatabaseRepository;
use Kwai\Modules\News\Infrastructure\Repositories\StoryDatabaseRepository;
use Kwai\Modules\News\UseCases\CreateStory;
use Kwai\Modules\News\UseCases\CreateStoryCommand;
use Tests\DatabaseTrait;

uses(DatabaseTrait::class);
beforeEach(fn() => $this->withDatabase());

it('can create a story', function ()  {
    $command = new CreateStoryCommand();
    try {
        $command->application = 1;
        $command->timezone = 'Europe/Brussels';
        $command->publish_date = '2020-05-01 09:00:00';
        $command->promotion = 1;

        $content = new Content();
        $content->content = 'This is a test from CreateStoryTest';
        $content->format = DocumentFormat::MARKDOWN;
        $content->locale = Locale::NL;
        $content->summary = 'This is a test';
        $content->title = 'Test';
        $command->contents = [ $content ];

        $story = (new CreateStory(
            new StoryDatabaseRepository($this->db),
            new class($this->db) extends ApplicationDatabaseRepository {
                public function getById(int $id): Entity
                {
                    return new Entity(1, new Application(
                        title: 'Unit Test',
                            name: 'unit_test'
                    ));
                }
            },
            new class implements ImageRepository {
                public function getImages(int $id): Collection
                {
                    return collect();
                }
                public function removeImages(int $id): void
                {
                }
            }
        ))($command,
            new Creator(
                $id = 1,
                $name = new Name('Jigoro', 'Kano')
            )
        );
        expect($story)
            ->toBeInstanceOf(Entity::class)
        ;
        expect($story->domain())
            ->toBeInstanceOf(Story::class)
        ;
        /** @noinspection PhpUndefinedMethodInspection */
        $contents = $story->getContents();
        expect($contents)
            ->toBeInstanceOf(Collection::class)
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
        $this->fail((string) $e);
    } catch (AuthorNotFoundException $e) {
        $this->fail((string) $e);
    } catch (ApplicationNotFoundException $e) {
        $this->fail((string) $e);
    }
})
    ->skip(fn() => !$this->hasDatabase(), 'No database available')
;
