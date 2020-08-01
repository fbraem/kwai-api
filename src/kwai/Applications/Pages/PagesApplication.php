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
use Slim\Routing\RouteCollectorProxy;

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
    public function __construct()
    {
        parent::__construct('pages');
    }

    /**
     * @inheritDoc
     */
    public function createRoutes(RouteCollectorProxy $group): void
    {
        $group->get('', BrowsePagesAction::class)
            ->setName('pages.browse')
        ;
        $group->get('/{id:[0-9]+}', GetPageAction::class)
            ->setName('pages.get')
        ;
    }

    public function addDependencies(): void
    {
        parent::addDependencies();

        $this->addDependency('filesystem', new FileSystemDependency());
        $this->addDependency('converter', new ConvertDependency());
    }
}
