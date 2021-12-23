<?php
/**
 * @package Pages
 * @subpackage Infrastructure
 */
declare(strict_types=1);

namespace Kwai\Modules\Pages\Infrastructure\Repositories;

use Illuminate\Support\Collection;
use Kwai\Core\Infrastructure\Repositories\ImageRepository;
use League\Flysystem\Filesystem;

/**
 * Class PageImageRepository
 */
class PageImageRepository implements ImageRepository
{
    /**
     * PageImageRepository constructor.
     *
     * @param Filesystem $filesystem
     * @param string     $baseUrlPath
     */
    public function __construct(
        private Filesystem $filesystem,
        private string $baseUrlPath = ''
    ) {
    }

    /**
     * Return the path for the given id
     *
     * @param int $id
     * @return string
     */
    private static function getPath(int $id)
    {
        return "images/pages/$id";
    }

    /**
     * @inheritDoc
     */
    public function getImages(int $id): Collection
    {
        $images = collect($this->filesystem->listContents(self::getPath($id)));
        return $images->mapWithKeys(fn($image) =>
            [ $image['filename'] => $this->baseUrlPath . '/' . $image['path'] ]
        );
    }

    public function removeImages(int $id): void
    {
        $this->filesystem->deleteDir(self::getPath($id));
    }
}
