<?php /** @noinspection PhpUndefinedMethodInspection */
/**
 * Test PageDatabaseRepository
 */
declare(strict_types=1);

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\Creator;
use Kwai\Core\Domain\ValueObjects\DocumentFormat;
use Kwai\Core\Domain\ValueObjects\Name;
use Kwai\Core\Domain\ValueObjects\Text;
use Kwai\Core\Domain\ValueObjects\Locale;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Pages\Domain\Exceptions\ApplicationNotFoundException;
use Kwai\Modules\Pages\Domain\Exceptions\PageNotFoundException;
use Kwai\Modules\Pages\Domain\Page;
use Kwai\Modules\Pages\Infrastructure\Repositories\ApplicationDatabaseRepository;
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
        $context->author = new Creator(
            1,
            new Name(
                'Jigoro',
                'Kano'
            )
        );
        try {
            $context->application = (new ApplicationDatabaseRepository($context->db))->getById(1);
        } catch (ApplicationNotFoundException $e) {
        } catch (RepositoryException $e) {
        }
    }
});

it('can create a new page', function () use ($context) {
    if (! isset($context->author)) {
        $this->assertTrue(false, 'No author');
    }
    if (! isset($context->application)) {
        $this->assertTrue(false, 'No application');
    }

    $repo = new PageDatabaseRepository($context->db);
    try {
        $page = $repo->create(new Page((object)[
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
            'priority' => 0,
            'application' => $context->application
        ]));
        $context->pageId = $page->id();
        expect($page)
            ->toBeInstanceOf(Entity::class)
        ;
        expect($page->domain())
            ->toBeInstanceOf(Page::class)
        ;
    } catch (Exception $e) {
        $this->assertTrue(false, (string) $e);
    }
})
    ->skip(!Context::hasDatabase(), 'No database available')
;

it('can find a page for a given id', function () use ($context) {
    $repo = new PageDatabaseRepository($context->db);
    try {
        $page = $repo->getById($context->pageId);
        expect($page)
            ->toBeInstanceOf(Entity::class)
        ;
        expect($page->domain())
            ->toBeInstanceOf(Page::class)
        ;
    } catch (PageNotFoundException $nfe) {
        $this->assertTrue(false, 'Page not found');
    } catch (RepositoryException $e) {
        $this->assertTrue(false, (string) $e);
    }
})
    ->skip(!Context::hasDatabase(), 'No database available')
    ->depends('it it can create a new page')
;

it('can update a page', function () use ($context) {
    $repo = new PageDatabaseRepository($context->db);
    try {
        $oldPage = $repo->getById($context->pageId);
        /** @var Text $oldText */
        $oldText = $oldPage->getContents()[0];
        $newText = new Text(
            $oldText->getLocale(),
            $oldText->getFormat(),
            'Test Update',
            $oldText->getSummary(),
            $oldText->getContent(),
            $oldText->getAuthor()
        );
        $page = new Entity(
            $oldPage->id(),
            new Page((object) [
                'enabled' => $oldPage->isEnabled(),
                'contents' => [
                    $newText
                ],
                'images' => $oldPage->getImages(),
                'priority' => $oldPage->getPriority(),
                'application' => $oldPage->getApplication()
            ])
        );
        $repo->update($page);
        $newPage = $repo->getById($context->pageId);
        expect($newPage->getContents()[0]->getTitle())
            ->toBe('Test Update')
        ;
    } catch (PageNotFoundException $nfe) {
        $this->assertTrue(false, 'Page not found');
    } catch (RepositoryException $e) {
        $this->assertTrue(false, (string) $e);
    }
})
    ->skip(!Context::hasDatabase(), 'No database available')
    ->depends('it it can create a new page')
;

it('can delete a page', function () use ($context) {
    $repo = new PageDatabaseRepository($context->db);
    try {
        $page = $repo->getById($context->pageId);
        $repo->remove($page);
        $this->expectNotToPerformAssertions();
    } catch (PageNotFoundException $nfe) {
        $this->assertTrue(false, 'Page not found');
    } catch (RepositoryException $e) {
        $this->assertTrue(false, (string) $e);
    }
})
    ->skip(!Context::hasDatabase(), 'No database available')
    ->depends('it it can create a new page')
;

test('getById throws a page not found exception', function () use ($context) {
    $repo = new PageDatabaseRepository($context->db);
    try {
        /** @noinspection PhpUnhandledExceptionInspection */
        $repo->getById($context->pageId);
    } catch (RepositoryException $e) {
        $this->assertTrue(false, (string) $e);
    }
})
    ->throws(PageNotFoundException::class)
    ->depends('it it can delete a page')
    ->skip(!Context::hasDatabase(), 'No database available')
;
