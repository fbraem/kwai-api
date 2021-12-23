<?php
/**
 * @package Applications
 * @subpackage Coach
 */
declare(strict_types=1);

namespace Kwai\Applications;

use Kwai\Modules\Coaches\Presentation\REST\BrowseCoachesAction;
use Kwai\Modules\Coaches\Presentation\REST\CreateCoachAction;
use Kwai\Modules\Coaches\Presentation\REST\GetCoachAction;
use Kwai\Core\Infrastructure\Presentation\Router;
use Kwai\Modules\Coaches\Presentation\REST\UpdateCoachAction;

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
                requirements: [ 'id' => '\d+' ]
            )
            ->post(
                'coaches.create',
                '/coaches',
                CreateCoachAction::class,
                [
                    'auth' => true
                ]
            )
            ->patch(
                'coaches.update',
                '/coaches/{id}',
                UpdateCoachAction::class,
                [
                    'auth' => true
                ],
                [
                    'id' => '\d+',
                ]
            )
        ;
    }
}
