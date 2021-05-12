<?php
/**
 * @package Applications
 * @subpackage Portal
 */
declare(strict_types=1);

namespace Kwai\Applications;

use Kwai\Modules\Applications\Presentation\REST\BrowseApplicationAction;
use Kwai\Core\Infrastructure\Presentation\Router;
use Kwai\Modules\Applications\Presentation\REST\GetApplicationAction;
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
                'portal.applications.browse',
                '/portal/applications',
                fn (ContainerInterface $container) => new BrowseApplicationAction($container)
            )
            ->get(
                'portal.applications.get',
                '/portal/applications/{id}',
                fn (ContainerInterface $container) => new GetApplicationAction($container),
                requirements: [
                    'id' => '\d+'
                ]
            )
        ;
    }
}
