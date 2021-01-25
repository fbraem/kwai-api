<?php
/**
 * @package Applications
 * @subpackage Pages
 */
declare(strict_types=1);

namespace Kwai\Applications;

use Kwai\Applications\Pages\Actions\BrowsePagesAction;
use Kwai\Applications\Pages\Actions\GetPageAction;
use Kwai\Core\Infrastructure\Dependencies\ConvertDependency;
use Kwai\Core\Infrastructure\Dependencies\FileSystemDependency;
use Kwai\Core\Infrastructure\Presentation\Router;
use Kwai\Modules\Pages\Presentation\REST\CreatePageAction;
use Kwai\Modules\Pages\Presentation\REST\UpdatePageAction;
use Psr\Container\ContainerInterface;

/**
 * Class PageApplication
 *
 * Application for browsing/reading pages.
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
            ->post(
                'pages.create',
                '/pages',
                fn(ContainerInterface $container) => new CreatePageAction($container),
                [
                    'auth' => true
                ]
            )
            ->patch(
                'pages.update',
                '/pages/{id}',
                fn(ContainerInterface $container) => new UpdatePageAction($container),
                [
                    'auth' => true
                ],
                [
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
