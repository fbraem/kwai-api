<?php
/**
 * @package Applications
 * @subpackage Club
 */
declare(strict_types=1);

namespace Kwai\Applications;

use Kwai\Modules\Club\Presentation\REST\BrowseMembersAction;
use Kwai\Core\Infrastructure\Presentation\Router;

/**
 * Class ClubApplication
 *
 * Application for a club
 */
class ClubApplication extends Application
{
    public function createRouter(): Router
    {
        return Router::create()
            ->get(
                'club.members.browse',
                '/club/members',
                BrowseMembersAction::class,
                [
                    'auth' => true
                ]
            )
        ;
    }
}
