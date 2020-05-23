<?php
/**
 * @package Applications
 * @subpackage Site
 */
declare(strict_types=1);

namespace Kwai\Applications\Site;

use Kwai\Applications\Application;
use Kwai\Applications\Site\Actions\BrowseApplicationAction;
use Kwai\Applications\Site\Actions\BrowseStoriesAction;
use Kwai\Applications\Site\Actions\GetApplicationAction;
use Kwai\Core\Infrastructure\Dependencies\ConvertDependency;
use Kwai\Core\Infrastructure\Dependencies\FileSystemDependency;
use Slim\Routing\RouteCollectorProxy;

/**
 * Class SiteApplication
 */
class SiteApplication extends Application
{
    public function __construct()
    {
        parent::__construct('site');
    }

    public function createRoutes(RouteCollectorProxy $group): void
    {
        $group->get('/stories', BrowseStoriesAction::class)
            ->setName('site.news.browse')
        ;
        $group->get('/applications', BrowseApplicationAction::class)
            ->setName('site.applications.browse')
        ;
    }

    public function addDependencies(): void
    {
        parent::addDependencies();

        $this->addDependency('filesystem', new FileSystemDependency());
        $this->addDependency('converter', new ConvertDependency());
    }
}
