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
use Kwai\Modules\Pages\Domain\Page;
use Kwai\Modules\Pages\Repositories\ApplicationRepository;
use Kwai\Modules\Pages\Repositories\AuthorRepository;
use Kwai\Modules\Pages\Repositories\PageRepository;

/**
 * Class CreatePage
 *
 * Use case for creating a page.
 */
class CreatePage
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
     * @param CreatePageCommand $command
     * @return Entity<Page>
     * @throws ApplicationNotFoundException
     * @throws AuthorNotFoundException
     * @throws RepositoryException
     */
    public function __invoke(CreatePageCommand $command): Entity
    {
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

        $page = $this->pageRepo->create(new Page(
            (object) [
                'enabled' => $command->enabled,
                'contents' => $contents,
                'priority' => $command->priority,
                'remark' => $command->remark,
                'application' => $app
            ]
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
     * @param AuthorRepository      $authorRepo
     * @param ImageRepository       $imageRepo
     * @return CreatePage
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
