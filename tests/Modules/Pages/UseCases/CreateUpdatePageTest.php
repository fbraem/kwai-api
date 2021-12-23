<?php

declare(strict_types=1);

use Illuminate\Support\Collection;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\Creator;
use Kwai\Core\Domain\ValueObjects\Name;
use Kwai\Core\Infrastructure\Repositories\ImageRepository;
use Kwai\Core\UseCases\Content;
use Kwai\Modules\Pages\Domain\Page;
use Kwai\Modules\Pages\Infrastructure\Repositories\ApplicationDatabaseRepository;
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
$context->creator = new Creator(1, new Name('Jigoro', 'Kano'));

it('can create a page', function () use ($context) {
    $command = new CreatePageCommand();
    $command->application = 1;
    $content = new Content();
    $content->title = 'Test title';
    $content->summary = 'This is a summary';
    $content->content = 'This is the content of the page';
    $command->contents[] = $content;

    try {
        $page = CreatePage::create(
            new PageDatabaseRepository($context->db),
            new ApplicationDatabaseRepository($context->db),
            new class implements ImageRepository {
                public function getImages(int $id): Collection
                {
                    return collect([]);
                }

                public function removeImages(int $id): void
                {
                }
            }
        )($command, $context->creator);
        expect($page)
            ->toBeInstanceOf(Entity::class)
            ->and($page->domain())
            ->toBeInstanceOf(Page::class)
        ;
        $context->id = $page->id();
    } catch (Exception $e) {
        $this->fail((string) $e);
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
    $content->content = 'This is the content of the page';
    $command->contents[] = $content;

    try {
        $page = UpdatePage::create(
            new PageDatabaseRepository($context->db),
            new ApplicationDatabaseRepository($context->db),
            new class implements ImageRepository {
                public function getImages(int $id): Collection
                {
                    return collect([]);
                }

                public function removeImages(int $id): void
                {
                }
            }
        )($command, $context->creator);
        expect($page)
            ->toBeInstanceOf(Entity::class)
        ;
        /** @noinspection PhpUndefinedMethodInspection */
        expect($page->getContents()[0]->getSummary())
            ->toBe('This is an updated summary')
        ;
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
})
    ->depends('it can create a page')
    ->skip(!Context::hasDatabase(), 'No database available')
;
