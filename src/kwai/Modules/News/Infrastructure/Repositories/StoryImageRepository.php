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

    /**
     * StoryImageRepository constructor.
     *
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * @inheritDoc
     */
    public function getImages(int $id): array
    {
        $result = [];
        $images = $this->filesystem->listContents("images/news/$id");
        if (count($images) > 0) {
            foreach ($images as $image) {
                $result[$image['filename']] = '/files/' . $image['path'];
            }
        }
        return $result;
    }
}
