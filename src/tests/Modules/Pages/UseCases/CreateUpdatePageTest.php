<?php

declare(strict_types=1);

use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Repositories\ImageRepository;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Core\UseCases\Content;
use Kwai\Modules\Pages\Domain\Exceptions\ApplicationNotFoundException;
use Kwai\Modules\Pages\Domain\Exceptions\AuthorNotFoundException;
use Kwai\Modules\Pages\Domain\Exceptions\PageNotFoundException;
use Kwai\Modules\Pages\Domain\Page;
use Kwai\Modules\Pages\Infrastructure\Repositories\ApplicationDatabaseRepository;
use Kwai\Modules\Pages\Infrastructure\Repositories\AuthorDatabaseRepository;
use Kwai\Modules\Pages\Infrastructure\Repositories\PageDatabaseRepository;
use Kwai\Modules\Pages\UseCases\CreatePage;
use Kwai\Modules\Pages\UseCases\CreatePageCommand;
use Kwai\Modules\Pages\UseCases\UpdatePage;
use Kwai\Modules\Pages\UseCases\UpdatePageCommand;
use Tests\Context;

/**
 * Context for all tests in this file
 * + db: Database connection
 * + id: Id of the created page
 */
$context = Context::createContext();

it('can create a page', function () use ($context) {
    $command = new CreatePageCommand();
    $command->application = 1;
    $content = new Content();
    $content->title = 'Test title';
    $content->summary = 'This is a summary';
    $content->author = 1;
    $content->content = 'This is the content of the page';
    $command->contents[] = $content;

    try {
        $page = CreatePage::create(
            new PageDatabaseRepository($context->db),
            new ApplicationDatabaseRepository($context->db),
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
        )($command);
        expect($page)
            ->toBeInstanceOf(Entity::class)
            ->and($page->domain())
            ->toBeInstanceOf(Page::class)
        ;
        $context->id = $page->id();
    } catch (RepositoryException $e) {
        $this->assertTrue(false, (string) $e);
    } catch (ApplicationNotFoundException $e) {
        $this->assertTrue(false, (string) $e);
    } catch (AuthorNotFoundException $e) {
        $this->assertTrue(false, (string) $e);
    }
})
    ->skip(!Context::hasDatabase(), 'No database available')
;

it('can update a page', function () use ($context) {
    $command = new UpdatePageCommand();
    $command->id = $context->id;
    $command->application = 1;
    $content = new Content();
    $content->title = 'Test title';
    $content->summary = 'This is an updated summary';
    $content->author = 1;
    $content->content = 'This is the content of the page';
    $command->contents[] = $content;

    try {
        $page = UpdatePage::create(
            new PageDatabaseRepository($context->db),
            new ApplicationDatabaseRepository($context->db),
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
        )($command);
        expect($page)
            ->toBeInstanceOf(Entity::class)
        ;
        /** @noinspection PhpUndefinedMethodInspection */
        expect($page->getContents()[0]->getSummary())
            ->toBe('This is an updated summary')
        ;
    } catch (RepositoryException $e) {
        $this->assertTrue(false, (string) $e);
    } catch (ApplicationNotFoundException $e) {
        $this->assertTrue(false, (string) $e);
    } catch (AuthorNotFoundException $e) {
        $this->assertTrue(false, (string) $e);
    } catch (PageNotFoundException $e) {
        $this->assertTrue(false, (string) $e);
    }
})
    ->depends('it it can create a page')
    ->skip(!Context::hasDatabase(), 'No database available')
;
