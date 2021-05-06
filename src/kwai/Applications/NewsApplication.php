<?php
/**
 * @package Applications
 * @subpackage News
 */
declare(strict_types=1);

namespace Kwai\Applications;

use Kwai\Modules\News\Presentation\REST\BrowseStoriesAction;
use Kwai\Modules\News\Presentation\REST\GetArchiveAction;
use Kwai\Modules\News\Presentation\REST\GetStoryAction;
use Kwai\Core\Infrastructure\Dependencies\ConvertDependency;
use Kwai\Core\Infrastructure\Dependencies\FileSystemDependency;
use Kwai\Core\Infrastructure\Presentation\Router;
use Psr\Container\ContainerInterface;

/**
 * Class NewsApplication
 */
class NewsApplication extends Application
{
    public function addDependencies(): void
    {
        parent::addDependencies();

        $this->addDependency('filesystem', new FileSystemDependency());
        $this->addDependency('converter', new ConvertDependency());
    }

    /**
     * @inheritDoc
     */
    public function createRouter(): Router
    {
        return Router::create()
            ->get(
                'news.browse',
                '/news/stories',
                fn (ContainerInterface $container) => new BrowseStoriesAction($container)
            )
            ->get(
                'news.get',
                '/news/stories/{id}',
                fn (ContainerInterface $container) => new GetStoryAction($container),
                requirements: [
                    'id' => '\d+'
                ]
            )
            ->get(
                'news.archive',
                '/news/archive',
                fn (ContainerInterface $container) => new GetArchiveAction($container)
            )
        ;
    }
}
