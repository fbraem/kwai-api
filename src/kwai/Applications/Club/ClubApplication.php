<?php
/**
 * @package Applications
 * @subpackage Club
 */
declare(strict_types=1);

namespace Kwai\Applications\Site;

use Kwai\Applications\Application;
use Kwai\Applications\Site\Actions\BrowseStoriesAction;
use Kwai\Core\Infrastructure\Dependencies\ConvertDependency;
use Kwai\Core\Infrastructure\Dependencies\FileSystemDependency;
use Kwai\Modules\Applications\Presentation\GetApplicationWithNameAction;
use Slim\Routing\RouteCollectorProxy;

/**
 * Class ClubApplication
 */
class ClubApplication extends Application
{
    public function __construct()
    {
        parent::__construct('club');
    }

    public function createRoutes(RouteCollectorProxy $group): void
    {
        $group->get('/stories', BrowseStoriesAction::class)
            ->setName('club.news.browse')
        ;
        $group->get('/applications', GetApplicationWithNameAction::class)
            ->setName('club.applications.get')
            ->setArgument('application', 'club')
        ;
    }

    public function addDependencies(): void
    {
        parent::addDependencies();

        $this->addDependency('filesystem', new FileSystemDependency());
        $this->addDependency('converter', new ConvertDependency());
    }
}
