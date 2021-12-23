<?php
declare(strict_types=1);

namespace Tests\Modules\News\UseCases;

use Exception;
use Kwai\Core\Infrastructure\Repositories\ImageRepository;
use Kwai\Modules\News\Infrastructure\Repositories\StoryDatabaseRepository;
use Kwai\Modules\News\UseCases\BrowseStories;
use Kwai\Modules\News\UseCases\BrowseStoriesCommand;
use Tests\Context;
use Illuminate\Support\Collection;

$context = Context::createContext();

it('can browse stories', function () use ($context) {
    $command = new BrowseStoriesCommand();

    try {
        [$count, $stories] = (new BrowseStories(
            new StoryDatabaseRepository($context->db),
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
    ->skip(!Context::hasDatabase(), 'No database available')
;

it('can browse stories of a user', function () use ($context) {
    $command = new BrowseStoriesCommand();
    $command->userUid = 1;

    try {
        [$count, $stories] = (new BrowseStories(
            new StoryDatabaseRepository($context->db),
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
    ->skip(!Context::hasDatabase(), 'No database available')
;
