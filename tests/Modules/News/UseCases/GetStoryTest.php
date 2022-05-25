<?php
declare(strict_types=1);

namespace Tests\Modules\News\UseCases;

use Illuminate\Support\Collection;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Repositories\ImageRepository;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\News\Domain\Exceptions\StoryNotFoundException;
use Kwai\Modules\News\Infrastructure\Repositories\StoryDatabaseRepository;
use Kwai\Modules\News\UseCases\GetStory;
use Kwai\Modules\News\UseCases\GetStoryCommand;
use Tests\DatabaseTrait;

uses(DatabaseTrait::class);
beforeEach(fn() => $this->withDatabase());

it('can get a story', function () {
    $command = new GetStoryCommand();
    $command->id = 1;

    try {
        $story = (new GetStory(
            new StoryDatabaseRepository($this->db),
            new class implements ImageRepository {
                public function getImages(int $id): Collection
                {
                    return collect();
                }

                public function removeImages(int $id): void
                {
                }
            }
        ))($command);
        expect($story)
            ->toBeInstanceOf(Entity::class)
        ;
    } catch (RepositoryException $e) {
        $this->fail((string) $e);
    } catch (StoryNotFoundException $e) {
        $this->fail((string) $e);
    }
})
    ->skip(fn() => !$this->hasDatabase(), 'No database available')
;
