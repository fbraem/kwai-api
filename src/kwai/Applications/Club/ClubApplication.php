<?php
/**
 * @package Applications
 * @subpackage Club
 */
declare(strict_types=1);

namespace Kwai\Applications\Club;

use Kwai\Applications\Club\Actions\BrowsePagesAction;
use Kwai\Applications\Club\Actions\BrowseStoriesAction;
use Kwai\Applications\Application;
use Kwai\Core\Infrastructure\Dependencies\ConvertDependency;
use Kwai\Core\Infrastructure\Dependencies\FileSystemDependency;
use Kwai\Core\Infrastructure\Presentation\Router;
use Psr\Container\ContainerInterface;

/**
 * Class ClubApplication
 */
class ClubApplication extends Application
{
    const APP = 'club';

    public function createRouter(): Router
    {
        return Router::create()
            ->get(
                'club.news.browse',
                '/club/stories',
                fn(ContainerInterface $container) => new BrowseStoriesAction($container)
            )
            ->get(
                'club.pages.browse',
                '/club/pages',
                fn(ContainerInterface $container) => new BrowsePagesAction($container)
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
