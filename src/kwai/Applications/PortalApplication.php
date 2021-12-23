<?php
/**
 * @package Applications
 * @subpackage Portal
 */
declare(strict_types=1);

namespace Kwai\Applications;

use Kwai\Core\Infrastructure\Presentation\Router;
use Kwai\Modules\Applications\Presentation\REST\BrowseApplicationAction;
use Kwai\Modules\Applications\Presentation\REST\GetApplicationAction;
use Kwai\Modules\Applications\Presentation\REST\UpdateApplicationAction;

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
                BrowseApplicationAction::class
            )
            ->get(
                'portal.applications.get',
                '/portal/applications/{id}',
                GetApplicationAction::class,
                requirements: [
                    'id' => '\d+'
                ]
            )
            ->patch(
                'portal.applications.update',
                '/portal/applications/{id}',
                UpdateApplicationAction::class,
                requirements: [
                    'id' => '\d+'
                ],
                extra: [
                    'auth' => true
                ]
            )
        ;
    }
}
