<?php
/**
 * Test PageDatabaseRepository
 */
declare(strict_types=1);

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\DocumentFormat;
use Kwai\Core\Domain\ValueObjects\Text;
use Kwai\Core\Domain\ValueObjects\Locale;
use Kwai\Modules\Pages\Domain\Exceptions\ApplicationNotFoundException;
use Kwai\Modules\Pages\Domain\Exceptions\AuthorNotFoundException;
use Kwai\Modules\Pages\Domain\Exceptions\PageNotFoundException;
use Kwai\Modules\Pages\Domain\Page;
use Kwai\Modules\Pages\Infrastructure\Repositories\ApplicationDatabaseRepository;
use Kwai\Modules\Pages\Infrastructure\Repositories\AuthorDatabaseRepository;
use Kwai\Modules\Pages\Infrastructure\Repositories\PageDatabaseRepository;
use Tests\Context;

/**
 * Context for all tests in this file
 * + db: Database connection
 * + author: Author used to create/update pages
 * + pageId: Id of the page that was last created
 */
$context = Context::createContext();

beforeAll(function () use ($context) {
    if (Context::hasDatabase()) {
        try {
            $context->author = (new AuthorDatabaseRepository($context->db))->getById(1);
        } catch (AuthorNotFoundException $e) {
        }
        try {
            $context->application = (new ApplicationDatabaseRepository($context->db))->getById(1);
        } catch (ApplicationNotFoundException $e) {
        }
    }
});

it('can create a new page', function () use ($context) {
    $repo = new PageDatabaseRepository($context->db);
    $page = $repo->create(new Page((object) [
        'enabled' => true,
        'contents' => [
            new Text(
                new Locale('nl'),
                new DocumentFormat('md'),
                'Test Page',
                'This is a test summary',
                'This is the content',
                $context->author
            ),
        ],
        'images' => [],
        'priority' => 0
    ]));
    $context->pageId = $page->id();
    assertInstanceOf(Entity::class, $page);
    assertInstanceOf(Page::class, $page->domain());
})
    ->skip(!Context::hasDatabase(), 'No database available')
;

it('can find a page for a given id', function () use ($context) {
    $repo = new PageDatabaseRepository($context->db);
    try {
        $page = $repo->getById($context->pageId);
        assertInstanceOf(Entity::class, $page);
    } catch (PageNotFoundException $nfe) {
        assertTrue(false, 'Page not found');
    }
})
    ->skip(!Context::hasDatabase(), 'No database available')
;

it('can update a page');

test('getById throws a page not found exception', function () use ($context) {
    $repo = new PageDatabaseRepository($context->db);
    $repo->getById(10000000);
})
    ->throws(PageNotFoundException::class)
    ->skip(!Context::hasDatabase(), 'No database available')
;
