<?php
/**
 * @package Modules
 * @subpackage Pages
 */
declare(strict_types=1);

namespace Kwai\Modules\Pages\UseCases;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Repositories\ImageRepository;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Pages\Domain\Exceptions\PageNotFoundException;
use Kwai\Modules\Pages\Domain\Page;
use Kwai\Modules\Pages\Repositories\PageRepository;

/**
 * Class GetPage
 *
 * Use case to read a page.
 */
class GetPage
{
    /**
     * GetPage constructor.
     *
     * @param PageRepository  $repo
     * @param ImageRepository $imageRepo
     */
    private function __construct(
        private PageRepository $repo,
        private ImageRepository $imageRepo
    ) {
    }

    /**
     * Executes the use case.
     *
     * @param $command
     * @return Entity<Page>
     * @throws RepositoryException
     * @throws PageNotFoundException
     */
    public function __invoke(GetPageCommand $command): Entity
    {
        $page = $this->repo->getById($command->id);
        $images = $this->imageRepo->getImages($command->id);
        /** @noinspection PhpUndefinedMethodInspection */
        $page->attachImages($images);
        return $page;
    }

    /**
     * Factory method
     *
     * @param PageRepository  $repo
     * @param ImageRepository $imageRepo
     * @return GetPage
     */
    public static function create(PageRepository $repo, ImageRepository $imageRepo) : self
    {
        return new self($repo, $imageRepo);
    }
}
