<?php
/**
 * @package Applications
 * @subpackage Page
 */
declare(strict_types=1);

namespace Kwai\Applications\Pages;

use Kwai\Applications\Application;
use Kwai\Applications\Pages\Actions\BrowsePagesAction;
use Kwai\Applications\Pages\Actions\GetPageAction;
use Kwai\Core\Infrastructure\Dependencies\ConvertDependency;
use Kwai\Core\Infrastructure\Dependencies\FileSystemDependency;
use Kwai\Core\Infrastructure\Presentation\Router;
use Psr\Container\ContainerInterface;

/**
 * Class PageApplication
 *
 * Application for browsing/reading pages.
 *
 * This application is temporary. In the future all other applications will
 * handle this themselves.
 */
class PagesApplication extends Application
{
    /**
     * @inheritDoc
     */
    public function createRouter(): Router
    {
        return Router::create()
            ->get(
                'pages.browse',
                '/pages',
                fn(ContainerInterface $container) => new BrowsePagesAction($container)
            )
            ->get(
                'pages.get',
                '/pages/{id}',
                fn(ContainerInterface $container) => new GetPageAction($container),
                requirements: [
                    'id' => '\d+'
                ]
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
