<?php
/**
 * @package Applications
 * @subpackage Coach
 */
declare(strict_types=1);

namespace Kwai\Applications\Coach;

use Kwai\Applications\Coach\Actions\BrowseTrainingsAction;
use Kwai\Applications\KwaiApplication;
use Kwai\Core\Infrastructure\Presentation\Router;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class CoachApplication
 *
 * Application for a coach
 */
class CoachApplication extends KwaiApplication
{
    public function addMiddlewares(): void
    {
        $this->addMiddleware(new class implements MiddlewareInterface {
            public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
            {

            }
        });
    }

    public function createRouter(): Router
    {
        $router = new Router();

        $router->options(
            'coach.trainings.options',
            '/coach/trainings/{id}',
            fn(ContainerInterface $container) => new BrowseTrainingsAction($container)
        );
        $router->get(
            'coach.trainings.get',
            '/coach/trainings/{id}',
            fn(ContainerInterface $container) => new BrowseTrainingsAction($container),
            [ 'auth' => true ],
            [ 'id' => '\d+' ]
        );

        return $router;
    }
}
