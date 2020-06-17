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
        $group->get('/stories', BrowseStoriesAction::class)
            ->setName('portal.news.browse')
        ;
        $group->get('/applications', BrowseApplicationAction::class)
            ->setName('portal.applications.browse')
        ;
    }

    public function addDependencies(): void
    {
        parent::addDependencies();

        $this->addDependency('filesystem', new FileSystemDependency());
        $this->addDependency('converter', new ConvertDependency());
    }
}
