<?php
/** @noinspection PhpUndefinedMethodInspection */
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
        } catch (ApplicationNotFoundException) {
        } catch (RepositoryException) {
        }
    }
});

it('can create a new page', function () use ($context) {
    if (! isset($context->author)) {
        $this->fail('No author');
    }
    if (! isset($context->application)) {
        $this->fail('No application');
    }

    $repo = new PageDatabaseRepository($context->db);
    try {
        $page = $repo->create(new Page(
            enabled: true,
            contents: collect([
                new Text(
                    locale: new Locale('nl'),
                    format: new DocumentFormat('md'),
                    title: 'Test Page',
                    summary: 'This is a test summary',
                    content: 'This is the content',
                    author: $context->author
                ),
            ]),
            application: $context->application
        ));
        expect($page)
            ->toBeInstanceOf(Entity::class)
        ;
        expect($page->domain())
            ->toBeInstanceOf(Page::class)
        ;
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
    return $page->id();
})
    ->skip(!Context::hasDatabase(), 'No database available')
;

it('can find a page for a given id', function ($id) use ($context) {
    $repo = new PageDatabaseRepository($context->db);
    try {
        $page = $repo->getById($id);
        expect($page)
            ->toBeInstanceOf(Entity::class)
        ;
        expect($page->domain())
            ->toBeInstanceOf(Page::class)
        ;
    } catch (PageNotFoundException) {
        $this->fail('Page not found');
    } catch (RepositoryException $e) {
        $this->fail((string) $e);
    }
})
    ->skip(!Context::hasDatabase(), 'No database available')
    ->depends('it can create a new page')
;

it('can update a page', function ($id) use ($context) {
    $repo = new PageDatabaseRepository($context->db);
    try {
        $oldPage = $repo->getById($id);
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
            new Page(
                enabled: $oldPage->isEnabled(),
                contents: collect([$newText]),
                images: $oldPage->getImages(),
                priority: $oldPage->getPriority(),
                application: $oldPage->getApplication()
            )
        );
        $repo->update($page);
        $newPage = $repo->getById($id);
        expect($newPage->getContents()[0]->getTitle())
            ->toBe('Test Update')
        ;
    } catch (PageNotFoundException) {
        $this->fail('Page not found');
    } catch (RepositoryException $e) {
        $this->fail((string) $e);
    }
})
    ->skip(!Context::hasDatabase(), 'No database available')
    ->depends('it can create a new page')
;

it('can delete a page', function ($id) use ($context) {
    $repo = new PageDatabaseRepository($context->db);
    try {
        $page = $repo->getById($id);
        $repo->remove($page);
        $this->expectNotToPerformAssertions();
    } catch (PageNotFoundException) {
        $this->fail('Page not found');
    } catch (RepositoryException $e) {
        $this->fail((string) $e);
    }
    return $id;
})
    ->skip(!Context::hasDatabase(), 'No database available')
    ->depends('it can create a new page')
;

test('getById throws a page not found exception', function ($id) use ($context) {
    $repo = new PageDatabaseRepository($context->db);
    try {
        /** @noinspection PhpUnhandledExceptionInspection */
        $repo->getById($id);
    } catch (RepositoryException $e) {
        $this->fail((string) $e);
    }
})
    ->throws(PageNotFoundException::class)
    ->depends('it can delete a page')
    ->skip(!Context::hasDatabase(), 'No database available')
;
