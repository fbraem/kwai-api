<?php
/**
 * @package Applications
 * @subpackage Club
 */
declare(strict_types=1);

namespace Kwai\Applications\Club;

use Kwai\Applications\Application;
use Kwai\Applications\Club\Actions\BrowsePagesAction;
use Kwai\Applications\Club\Actions\BrowseStoriesAction;
use Kwai\Core\Infrastructure\Dependencies\ConvertDependency;
use Kwai\Core\Infrastructure\Dependencies\FileSystemDependency;
use Kwai\Core\Infrastructure\Presentation\PreflightAction;
use Slim\Routing\RouteCollectorProxy;

/**
 * Class ClubApplication
 */
class ClubApplication extends Application
{
    const APP = 'club';

    public function __construct()
    {
        parent::__construct(self::APP);
    }

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
                    ->setName(self::APP . '.news.browse')
                    ->setArgument('application', self::APP)
                ;
            }
        );

        $group->group(
            '/pages',
            function (RouteCollectorProxy $pagesGroup) {
                $pagesGroup
                    ->options('', PreflightAction::class)
                ;
                $pagesGroup
                    ->get('', BrowsePagesAction::class)
                    ->setName(self::APP . '.pages.browse')
                    ->setArgument('application', self::APP)
                ;
            }
        );
    }

    public function addDependencies(): void
    {
        parent::addDependencies();

        $this->addDependency('filesystem', new FileSystemDependency());
        $this->addDependency('converter', new ConvertDependency());
    }
}
