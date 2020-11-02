<?php

declare(strict_types=1);

use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Repositories\ImageRepository;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Pages\Domain\Exceptions\PageNotFoundException;
use Kwai\Modules\Pages\Domain\Page;
use Kwai\Modules\Pages\Infrastructure\Repositories\PageDatabaseRepository;
use Kwai\Modules\Pages\UseCases\GetPage;
use Kwai\Modules\Pages\UseCases\GetPageCommand;
use Tests\Context;

/**
 * Context for all tests in this file
 * + db: Database connection
 */
$context = Context::createContext();

it('can get a page', function () use ($context) {
    $command = new GetPageCommand();
    try {
        $command->id = 1;
        $page = GetPage::create(
            new PageDatabaseRepository($context->db),
            new class implements ImageRepository {
                public function getImages(int $id): array
                {
                    return [];
                }
                public function removeImages(int $id): void
                {
                }
            }
        )($command);
        expect($page)
            ->toBeInstanceOf(Entity::class);
        expect($page->domain())
            ->toBeInstanceOf(Page::class);
    } catch (QueryException $e) {
        $this->assertTrue(false, (string) $e);
    } catch (RepositoryException $e) {
        $this->assertTrue(false, (string) $e);
    } catch (PageNotFoundException $e) {
        $this->assertTrue(false, (string) $e);
    }
})
    ->skip(!Context::hasDatabase(), 'No database available')
;
