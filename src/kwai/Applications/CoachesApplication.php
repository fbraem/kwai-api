<?php
/**
 * @package Applications
 * @subpackage Coach
 */
declare(strict_types=1);

namespace Kwai\Applications;

use Kwai\Modules\Coaches\Presentation\REST\BrowseCoachesAction;
use Kwai\Modules\Coaches\Presentation\REST\GetCoachAction;
use Kwai\Core\Infrastructure\Presentation\PreflightAction;
use Kwai\Core\Infrastructure\Presentation\Router;
use Psr\Container\ContainerInterface;

/**
 * Class CoachApplication
 *
 * Application for a coach
 */
class CoachesApplication extends Application
{
    public function createRouter(): Router
    {
        $router = new Router();

        $router->options(
            'coaches.browse.options',
            '/coaches',
            fn () => new PreflightAction()
        );
        $router->get(
            'coaches.browse',
            '/coaches',
            fn (ContainerInterface $container) => new BrowseCoachesAction($container),
        );

        $router->options(
            'coaches.get.options',
            '/coaches/{id}',
            fn () => new PreflightAction()
        );
        $router->get(
            'coaches.get',
            '/coaches/{id}',
            fn (ContainerInterface $container) => new GetCoachAction($container),
            [ 'id' => '\d+' ]
        );

        return $router;
    }
}
