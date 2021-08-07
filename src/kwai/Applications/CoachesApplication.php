<?php
/**
 * @package Applications
 * @subpackage Coach
 */
declare(strict_types=1);

namespace Kwai\Applications;

use Kwai\Modules\Coaches\Presentation\REST\BrowseCoachesAction;
use Kwai\Modules\Coaches\Presentation\REST\GetCoachAction;
use Kwai\Core\Infrastructure\Presentation\Router;

/**
 * Class CoachApplication
 *
 * Application for a coach
 */
class CoachesApplication extends Application
{
    public function createRouter(): Router
    {
        return Router::create()
            ->get(
                'coaches.browse',
                '/coaches',
                BrowseCoachesAction::class,
            )
            ->get(
                'coaches.get',
                '/coaches/{id}',
                GetCoachAction::class,
                [ 'id' => '\d+' ]
            )
        ;
    }
}
