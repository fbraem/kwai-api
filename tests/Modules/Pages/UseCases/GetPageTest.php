<?php
declare(strict_types=1);

use Illuminate\Support\Collection;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Repositories\ImageRepository;
use Kwai\Modules\Pages\Domain\Page;
use Kwai\Modules\Pages\Infrastructure\Repositories\PageDatabaseRepository;
use Kwai\Modules\Pages\UseCases\GetPage;
use Kwai\Modules\Pages\UseCases\GetPageCommand;
use Tests\DatabaseTrait;

uses(DatabaseTrait::class);
beforeEach(fn() => $this->withDatabase());

it('can get a page', function () {
    $command = new GetPageCommand();
    try {
        $command->id = 1;
        $page = GetPage::create(
            new PageDatabaseRepository($this->db),
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
        expect($page)
            ->toBeInstanceOf(Entity::class);
        expect($page->domain())
            ->toBeInstanceOf(Page::class);
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
})
    ->skip(fn() => !$this->hasDatabase(), 'No database available')
;
