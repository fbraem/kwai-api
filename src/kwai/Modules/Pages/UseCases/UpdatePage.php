<?php
/**
 * @package Pages
 * @subpackage UseCases
 */
declare(strict_types=1);

namespace Kwai\Modules\Pages\UseCases;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\DocumentFormat;
use Kwai\Core\Domain\ValueObjects\Locale;
use Kwai\Core\Domain\ValueObjects\Text;
use Kwai\Core\Infrastructure\Repositories\ImageRepository;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Pages\Domain\Exceptions\ApplicationNotFoundException;
use Kwai\Modules\Pages\Domain\Exceptions\AuthorNotFoundException;
use Kwai\Modules\Pages\Domain\Exceptions\PageNotFoundException;
use Kwai\Modules\Pages\Domain\Page;
use Kwai\Modules\Pages\Repositories\ApplicationRepository;
use Kwai\Modules\Pages\Repositories\AuthorRepository;
use Kwai\Modules\Pages\Repositories\PageRepository;

/**
 * Class UpdatePage
 *
 * Use case for updating a page.
 */
class UpdatePage
{
    private PageRepository $pageRepo;

    private ApplicationRepository $applicationRepo;

    private AuthorRepository $authorRepo;

    private ImageRepository $imageRepo;

    /**
     * SavePage constructor.
     *
     * @param PageRepository        $pageRepo
     * @param ApplicationRepository $applicationRepo
     * @param AuthorRepository      $authorRepo
     * @param ImageRepository       $imageRepo
     */
    private function __construct(
        PageRepository $pageRepo,
        ApplicationRepository $applicationRepo,
        AuthorRepository $authorRepo,
        ImageRepository $imageRepo
    ) {
        $this->pageRepo = $pageRepo;
        $this->applicationRepo = $applicationRepo;
        $this->authorRepo = $authorRepo;
        $this->imageRepo = $imageRepo;
    }

    /**
     * Executes the use case.
     *
     * @param UpdatePageCommand $command
     * @return Entity<Page>
     * @throws PageNotFoundException
     * @throws RepositoryException
     * @throws ApplicationNotFoundException
     * @throws AuthorNotFoundException
     */
    public function __invoke(UpdatePageCommand $command): Entity
    {
        $page = $this->pageRepo->getById($command->id);
        $app = $this->applicationRepo->getById($command->application);

        $contents = [];
        foreach ($command->contents as $text) {
            $author = $this->authorRepo->getById($text->author);
            $contents[] = new Text(
                new Locale($text->locale),
                new DocumentFormat($text->format),
                $text->title,
                $text->summary,
                $text->content,
                $author
            );
        }

        /** @noinspection PhpUndefinedMethodInspection */
        $traceableTime = $page->getTraceableTime();
        $traceableTime->markUpdated();

        $page = new Entity(
            $page->id(),
            new Page(
                (object) [
                    'enabled' => $command->enabled,
                    'contents' => $contents,
                    'priority' => $command->priority,
                    'remark' => $command->remark,
                    'application' => $app,
                    'traceableTime' => $traceableTime
                ]
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
     * @param AuthorRepository      $authorRepo
     * @param ImageRepository       $imageRepo
     * @return UpdatePage
     */
    public static function create(
        PageRepository $pageRepo,
        ApplicationRepository $applicationRepo,
        AuthorRepository $authorRepo,
        ImageRepository $imageRepo
    ) : self {
        return new self(
            $pageRepo,
            $applicationRepo,
            $authorRepo,
            $imageRepo
        );
    }
}
