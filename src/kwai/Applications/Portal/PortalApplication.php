<?php
/**
 * @package Applications
 * @subpackage Portal
 */
declare(strict_types=1);

namespace Kwai\Applications\Portal;

use Kwai\Applications\Club\Actions\BrowsePagesAction;
use Kwai\Applications\Application;
use Kwai\Applications\Portal\Actions\BrowseApplicationAction;
use Kwai\Applications\Portal\Actions\BrowseStoriesAction;
use Kwai\Core\Infrastructure\Dependencies\ConvertDependency;
use Kwai\Core\Infrastructure\Dependencies\FileSystemDependency;
use Kwai\Core\Infrastructure\Presentation\Router;
use Psr\Container\ContainerInterface;

/**
 * Class PortalApplication
 */
class PortalApplication extends Application
{
    public function createRouter(): Router
    {
        return Router::create()
            ->get(
                'portal.news.browse',
                '/portal/stories',
                fn(ContainerInterface $container) => new BrowseStoriesAction($container)
            )
            ->get(
                'portal.pages.browse',
                '/portal/pages',
                fn(ContainerInterface $container) => new BrowsePagesAction($container)
            )
            ->get(
                'portal.applications.browse',
                '/portal/applications',
                fn(ContainerInterface $container) => new BrowseApplicationAction($container)
            )
        ;
    }

    public function addDependencies(): void
    {
        parent::addDependencies();

        $this->addDependency('filesystem', new FileSystemDependency());
        $this->addDependency('converter', new ConvertDependency());
    }
}
