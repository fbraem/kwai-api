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
use Kwai\Core\Infrastructure\Presentation\PreflightAction;
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
        $group->group(
            '',
            function (RouteCollectorProxy $pagesGroup) {
                $pagesGroup
                    ->options('', PreflightAction::class)
                ;
                $pagesGroup
                    ->get('', BrowsePagesAction::class)
                    ->setName('pages.browse')
                ;
            }
        );

        $group->group(
            '/{id:[0-9]+}',
            function (RouteCollectorProxy $pageGroup) {
                $pageGroup
                    ->options('', PreflightAction::class)
                ;
                $pageGroup
                    ->get('', GetPageAction::class)
                    ->setName('pages.get')
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
