<?php
/**
 * @package Modules
 * @subpackage Pages
 */
declare(strict_types=1);

namespace Kwai\Modules\Pages\UseCases;

use Illuminate\Support\Collection;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\Creator;
use Kwai\Core\Domain\ValueObjects\DocumentFormat;
use Kwai\Core\Domain\ValueObjects\Locale;
use Kwai\Core\Domain\ValueObjects\Text;
use Kwai\Core\Infrastructure\Repositories\ImageRepository;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Pages\Domain\Exceptions\ApplicationNotFoundException;
use Kwai\Modules\Pages\Domain\Exceptions\PageNotFoundException;
use Kwai\Modules\Pages\Domain\Page;
use Kwai\Modules\Pages\Repositories\ApplicationRepository;
use Kwai\Modules\Pages\Repositories\PageRepository;

/**
 * Class UpdatePage
 *
 * Use case for updating a page.
 */
class UpdatePage
{
    /**
     * SavePage constructor.
     *
     * @param PageRepository        $pageRepo
     * @param ApplicationRepository $applicationRepo
     * @param ImageRepository       $imageRepo
     */
    private function __construct(
        private PageRepository $pageRepo,
        private ApplicationRepository $applicationRepo,
        private ImageRepository $imageRepo
    ) {
    }

    /**
     * Executes the use case.
     *
     * @param UpdatePageCommand $command
     * @param Creator           $creator
     * @return Entity<Page>
     * @throws ApplicationNotFoundException
     * @throws PageNotFoundException
     * @throws RepositoryException
     */
    public function __invoke(UpdatePageCommand $command, Creator $creator): Entity
    {
        $page = $this->pageRepo->getById($command->id);
        $app = $this->applicationRepo->getById($command->application);

        $contents = new Collection();
        foreach ($command->contents as $text) {
            $contents->push(new Text(
                locale: Locale::from($text->locale),
                format: DocumentFormat::from($text->format),
                title: $text->title,
                summary: $text->summary,
                content: $text->content,
                author: $creator
            ));
        }

        /** @noinspection PhpUndefinedMethodInspection */
        $traceableTime = $page->getTraceableTime();
        $traceableTime->markUpdated();

        $page = new Entity(
            $page->id(),
            new Page(
                enabled: $command->enabled,
                contents: $contents,
                priority: $command->priority,
                remark: $command->remark,
                application: $app,
                traceableTime: $traceableTime
            )
        );
        $this->pageRepo->update($page);

        $images = $this->imageRepo->getImages($command->id);
        /** @noinspection PhpUndefinedMethodInspection */
        $page->attachImages($images);

        return $page;
    }

    /**
     * Factory method
     *
     * @param PageRepository        $pageRepo
     * @param ApplicationRepository $applicationRepo
     * @param ImageRepository       $imageRepo
     * @return UpdatePage
     */
    public static function create(
        PageRepository $pageRepo,
        ApplicationRepository $applicationRepo,
        ImageRepository $imageRepo
    ) : self {
        return new self(
            $pageRepo,
            $applicationRepo,
            $imageRepo
        );
    }
}
