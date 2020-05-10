<?php
/**
 * @package Applications
 * @subpackage News
 */
declare(strict_types=1);

namespace Kwai\Applications\News;

use Kwai\Applications\Application;
use Kwai\Applications\News\Actions\BrowseStoriesAction;
use Kwai\Applications\News\Actions\GetArchiveAction;
use Kwai\Applications\News\Actions\GetStoryAction;
use Kwai\Core\Infrastructure\Dependencies\ConvertDependency;
use Kwai\Core\Infrastructure\Dependencies\FileSystemDependency;
use Slim\Routing\RouteCollectorProxy;

/**
 * Class NewsApplication
 */
class NewsApplication extends Application
{
    /**
     * NewsApplication constructor.
     */
    public function __construct()
    {
        parent::__construct('news');
    }

    public function addDependencies(): void
    {
        parent::addDependencies();

        $this->addDependency('filesystem', new FileSystemDependency());
        $this->addDependency('converter', new ConvertDependency());
    }

    /**
     * @inheritDoc
     */
    public function createRoutes(RouteCollectorProxy $group): void
    {
        $group->get('/stories', BrowseStoriesAction::class)
            ->setName('news.browse')
        ;
        $group->get('/stories/{id:[0-9]+}', GetStoryAction::class)
            ->setName('news.read')
        ;
        $group->get('/archive', GetArchiveAction::class)
            ->setName('news.archive')
        ;
    }
}
