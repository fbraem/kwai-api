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
use Kwai\Modules\Pages\Domain\Page;
use Kwai\Modules\Pages\Repositories\ApplicationRepository;
use Kwai\Modules\Pages\Repositories\PageRepository;

/**
 * Class CreatePage
 *
 * Use case for creating a page.
 */
class CreatePage
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
     * @param CreatePageCommand $command
     * @param Creator           $creator
     * @return Entity<Page>
     * @throws ApplicationNotFoundException
     * @throws RepositoryException
     */
    public function __invoke(CreatePageCommand $command, Creator $creator): Entity
    {
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

        $page = $this->pageRepo->create(new Page(
            enabled: $command->enabled,
            contents: $contents,
            priority: $command->priority,
            remark: $command->remark,
            application: $app
        ));

        $images = $this->imageRepo->getImages($page->id());
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
     * @return CreatePage
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
