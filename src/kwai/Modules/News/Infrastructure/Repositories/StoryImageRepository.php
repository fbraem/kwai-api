<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Kwai\Modules\News\Infrastructure\Repositories;

use Kwai\Core\Infrastructure\Repositories\ImageRepository;
use League\Flysystem\Filesystem;

/**
 * Class StoryImageRepository
 */
class StoryImageRepository implements ImageRepository
{
    private Filesystem $filesystem;

    private string $baseUrlPath;

    /**
     * StoryImageRepository constructor.
     *
     * @param Filesystem $filesystem
     * @param string     $baseUrlPath
     */
    public function __construct(Filesystem $filesystem, string $baseUrlPath = '')
    {
        $this->filesystem = $filesystem;
        $this->baseUrlPath = $baseUrlPath;
    }

    /**
     * Return the path for the given id
     *
     * @param int $id
     * @return string
     */
    private static function getPath(int $id)
    {
        return "images/news/$id";
    }

    /**
     * @inheritDoc
     */
    public function getImages(int $id): array
    {
        $result = [];
        $images = $this->filesystem->listContents(self::getPath($id));
        if (count($images) > 0) {
            foreach ($images as $image) {
                $result[$image['filename']] = $this->baseUrlPath . '/' . $image['path'];
            }
        }
        return $result;
    }

    public function removeImages(int $id): void
    {
        $this->filesystem->deleteDir(self::getPath($id));
    }
}
