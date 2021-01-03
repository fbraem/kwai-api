<?php
/**
 * @package Applications
 * @subpackage News
 */
declare(strict_types=1);

namespace Kwai\Applications\News;

use Kwai\Applications\KwaiApplication;
use Kwai\Applications\News\Actions\BrowseStoriesAction;
use Kwai\Applications\News\Actions\GetArchiveAction;
use Kwai\Applications\News\Actions\GetStoryAction;
use Kwai\Core\Infrastructure\Dependencies\ConvertDependency;
use Kwai\Core\Infrastructure\Dependencies\FileSystemDependency;
use Kwai\Core\Infrastructure\Presentation\PreflightAction;
use Kwai\Core\Infrastructure\Presentation\Router;
use Psr\Container\ContainerInterface;

/**
 * Class NewsApplication
 */
class NewsApplication extends KwaiApplication
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
            ->options(
                'news.browse.options',
                '/news/stories',
            fn() => new PreflightAction()
            )
            ->get(
                'news.browse',
                '/news/stories',
                fn(ContainerInterface $container) => new BrowseStoriesAction($container)
            )
            ->options(
                'news.get.options',
                '/news/stories/{id}',
                fn() => new PreflightAction(),
                requirements: [
                    'id' => '\d+'
                ]
            )
            ->get(
                'news.get',
                '/news/stories/{id}',
                fn(ContainerInterface $container) => new GetStoryAction($container),
                requirements: [
                    'id' => '\d+'
                ]
            )
            ->options(
                'news.archive.options',
                '/news/archive',
                fn() => new PreflightAction()
            )
            ->get(
                'news.archive',
                '/news/archive',
                fn(ContainerInterface $container) => new GetArchiveAction($container)
            )
        ;
    }
}
