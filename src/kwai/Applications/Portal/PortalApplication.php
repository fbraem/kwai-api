<?php
/**
 * @package Applications
 * @subpackage Portal
 */
declare(strict_types=1);

namespace Kwai\Applications\Portal;

use Kwai\Applications\Application;
use Kwai\Applications\Portal\Actions\BrowseApplicationAction;
use Kwai\Applications\Portal\Actions\BrowseStoriesAction;
use Kwai\Applications\Portal\Actions\GetApplicationAction;
use Kwai\Core\Infrastructure\Dependencies\ConvertDependency;
use Kwai\Core\Infrastructure\Dependencies\FileSystemDependency;
use Kwai\Core\Infrastructure\Presentation\PreflightAction;
use Slim\Routing\RouteCollectorProxy;

/**
 * Class PortalApplication
 */
class PortalApplication extends Application
{
    public function __construct()
    {
        parent::__construct('portal');
    }

    public function createRoutes(RouteCollectorProxy $group): void
    {
        $group->group(
            '/stories',
            function (RouteCollectorProxy $storiesGroup) {
                $storiesGroup
                    ->options('', PreflightAction::class)
                ;
                $storiesGroup->get('', BrowseStoriesAction::class)
                    ->setName('portal.news.browse')
                ;
            }
        );

        $group->group(
            '/applications',
            function (RouteCollectorProxy $applicationsGroup) {
                $applicationsGroup
                    ->options('', PreflightAction::class)
                ;
                $applicationsGroup
                    ->get('', BrowseApplicationAction::class)
                    ->setName('portal.applications.browse')
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
