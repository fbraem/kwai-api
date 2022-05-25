<?php
declare(strict_types=1);

namespace Tests\Modules\News\UseCases;

use Exception;
use Kwai\Core\Infrastructure\Repositories\ImageRepository;
use Kwai\Modules\News\Infrastructure\Repositories\StoryDatabaseRepository;
use Kwai\Modules\News\UseCases\BrowseStories;
use Kwai\Modules\News\UseCases\BrowseStoriesCommand;
use Illuminate\Support\Collection;
use Tests\DatabaseTrait;

uses(DatabaseTrait::class);
beforeEach(fn() => $this->withDatabase());

it('can browse stories', function () {
    $command = new BrowseStoriesCommand();

    try {
        [$count, $stories] = (new BrowseStories(
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
        expect($stories)
            ->toBeInstanceOf(Collection::class)
        ;
        expect($count)
            ->toBeInt()
        ;
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
})
    ->skip(fn() => !$this->hasDatabase(), 'No database available')
;

it('can browse stories of a user', function () {
    $command = new BrowseStoriesCommand();
    $command->userUid = 1;

    try {
        [$count, $stories] = (new BrowseStories(
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
        expect($stories)
            ->toBeInstanceOf(Collection::class)
        ;
        expect($count)
            ->toBeInt()
        ;
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
})
    ->skip(fn() => !$this->hasDatabase(), 'No database available')
;
