<?php
declare(strict_types=1);

namespace Tests\Modules\News\UseCases;

use Kwai\Core\Infrastructure\Repositories\ImageRepository;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\News\Domain\Exceptions\AuthorNotFoundException;
use Kwai\Modules\News\Infrastructure\Repositories\AuthorDatabaseRepository;
use Kwai\Modules\News\Infrastructure\Repositories\StoryDatabaseRepository;
use Kwai\Modules\News\UseCases\BrowseStories;
use Kwai\Modules\News\UseCases\BrowseStoriesCommand;
use Tests\Context;
use Tightenco\Collect\Support\Collection;

$context = Context::createContext();

it('can browse stories', function () use ($context) {
    $command = new BrowseStoriesCommand();

    try {
        [$count, $stories] = (new BrowseStories(
            new StoryDatabaseRepository($context->db),
            new AuthorDatabaseRepository($context->db),
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
        expect($stories)
            ->toBeInstanceOf(Collection::class)
        ;
        expect($count)
            ->toBeInt()
        ;
    } catch (QueryException $e) {
        $this->assertTrue(false, (string) $e);
    } catch (RepositoryException $e) {
        $this->assertTrue(false, (string) $e);
    } catch (AuthorNotFoundException $e) {
        $this->assertTrue(false, (string) $e);
    }
})
    ->skip(!Context::hasDatabase(), 'No database available')
;
