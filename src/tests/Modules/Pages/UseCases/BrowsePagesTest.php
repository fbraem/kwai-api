<?php

declare(strict_types=1);

use Illuminate\Support\Collection;
use Kwai\Core\Infrastructure\Repositories\ImageRepository;
use Kwai\Modules\Pages\Infrastructure\Repositories\AuthorDatabaseRepository;
use Kwai\Modules\Pages\Infrastructure\Repositories\PageDatabaseRepository;
use Kwai\Modules\Pages\UseCases\BrowsePages;
use Kwai\Modules\Pages\UseCases\BrowsePagesCommand;
use Tests\Context;

/**
 * Context for all tests in this file
 * + db: Database connection
 */
$context = Context::createContext();

it('can browse pages', function () use ($context) {
    $command = new BrowsePagesCommand();
    try {
        BrowsePages::create(
            new PageDatabaseRepository($context->db),
            new AuthorDatabaseRepository($context->db),
            new class implements ImageRepository {
                public function getImages(int $id): Collection
                {
                    return collect([]);
                }
                public function removeImages(int $id): void
                {
                }
            }
        )($command);
        test()->expectNotToPerformAssertions();
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
})
    ->skip(!Context::hasDatabase(), 'No database available')
;
