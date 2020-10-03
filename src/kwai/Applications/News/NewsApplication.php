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
use Kwai\Core\Infrastructure\Presentation\PreflightAction;
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
        $group->group(
            '/stories',
            function (RouteCollectorProxy $storiesGroup) {
                $storiesGroup
                    ->options('', PreflightAction::class)
                ;
                $storiesGroup
                    ->get('', BrowseStoriesAction::class)
                    ->setName('news.browse')
                ;
            }
        );

        $group->group(
            '/stories/{id:[0-9]+}',
            function (RouteCollectorProxy $storyGroup) {
                $storyGroup
                    ->options('', PreflightAction::class)
                ;
                $storyGroup
                    ->get('', GetStoryAction::class)
                    ->setName('news.read')
                ;
            }
        );

        $group->group(
            '/archive',
            function (RouteCollectorProxy $archiveGroup) {
                $archiveGroup
                    ->options('', PreflightAction::class)
                ;
                $archiveGroup
                    ->get('', GetArchiveAction::class)
                    ->setName('news.archive')
                ;
            }
        );
    }
}
