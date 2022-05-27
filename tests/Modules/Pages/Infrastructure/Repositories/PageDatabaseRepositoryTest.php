<?php
declare(strict_types=1);

use Illuminate\Support\Collection;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\Creator;
use Kwai\Core\Domain\ValueObjects\DocumentFormat;
use Kwai\Core\Domain\ValueObjects\Name;
use Kwai\Core\Domain\ValueObjects\Text;
use Kwai\Core\Domain\ValueObjects\Locale;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Pages\Domain\Application;
use Kwai\Modules\Pages\Domain\Exceptions\PageNotFoundException;
use Kwai\Modules\Pages\Domain\Page;
use Kwai\Modules\Pages\Infrastructure\Repositories\PageDatabaseRepository;
use Tests\DatabaseTrait;

uses(DatabaseTrait::class);
beforeEach(fn() => $this->withDatabase());

$author = new Creator(
    1,
    new Name(
        'Jigoro',
        'Kano'
    )
);

$application = new Entity(
    1,
    new Application('Test', 'Test')
);

it('can browse a pages', function () {
    $repo = new PageDatabaseRepository($this->db);
    try {
        $pages = $repo->getAll();
        expect($pages)
            ->toBeInstanceOf(Collection::class)
        ;
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
})
    ->skip(fn() => !$this->hasDatabase(), 'No database available')
;

it('can create a new page', function () use ($author, $application) {
    $repo = new PageDatabaseRepository($this->db);
    try {
        $page = $repo->create(new Page(
            application: $application,
            enabled: true,
            contents: collect([
                new Text(
                    locale: Locale::NL,
                    format: DocumentFormat::MARKDOWN,
                    title: 'Test Page',
                    author: $author,
                    summary: 'This is a test summary',
                    content: 'This is the content'
                ),
            ])
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
    ->skip(fn() => !$this->hasDatabase(), 'No database available')
;

it('can find a page for a given id', function ($id) {
    $repo = new PageDatabaseRepository($this->db);
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
    ->skip(fn() => !$this->hasDatabase(), 'No database available')
    ->depends('it can create a new page')
;

it('can update a page', function ($id) {
    $repo = new PageDatabaseRepository($this->db);
    try {
        $oldPage = $repo->getById($id);
        /** @var Text $oldText */
        $oldText = $oldPage->getContents()[0];
        $newText = new Text(
            locale: $oldText->getLocale(),
            format: $oldText->getFormat(),
            title: 'Test Update',
            author: $oldText->getAuthor(),
            summary: $oldText->getSummary(),
            content: $oldText->getContent()
        );
        $page = new Entity(
            $oldPage->id(),
            new Page(
                application: $oldPage->getApplication(),
                enabled: $oldPage->isEnabled(),
                contents: collect([$newText]),
                images: $oldPage->getImages(),
                priority: $oldPage->getPriority()
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
    ->skip(fn() => !$this->hasDatabase(), 'No database available')
    ->depends('it can create a new page')
;

it('can delete a page', function ($id) {
    $repo = new PageDatabaseRepository($this->db);
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
    ->skip(fn() => !$this->hasDatabase(), 'No database available')
    ->depends('it can create a new page')
;

test('getById throws a page not found exception', function ($id) {
    $repo = new PageDatabaseRepository($this->db);
    try {
        /** @noinspection PhpUnhandledExceptionInspection */
        $repo->getById($id);
    } catch (RepositoryException $e) {
        $this->fail((string) $e);
    }
})
    ->throws(PageNotFoundException::class)
    ->depends('it can delete a page')
    ->skip(fn() => !$this->hasDatabase(), 'No database available')
;
