<?php
/**
 * @package Applications
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Applications\Trainings;

use Kwai\Applications\KwaiApplication;
use Kwai\Applications\Trainings\Actions\BrowseDefinitionsAction;
use Kwai\Applications\Trainings\Actions\BrowseTrainingsAction;
use Kwai\Applications\Trainings\Actions\GetDefinitionAction;
use Kwai\Applications\Trainings\Actions\GetTrainingAction;
use Kwai\Core\Infrastructure\Dependencies\ConvertDependency;
use Kwai\Core\Infrastructure\Presentation\PreflightAction;
use Kwai\Core\Infrastructure\Presentation\Router;
use Psr\Container\ContainerInterface;

/**
 * Class TrainingsApplication
 *
 * Public application for trainings.
 */
class TrainingsApplication extends KwaiApplication
{
    public function createRouter(): Router
    {
        $router = new Router();

        $router
            ->options(
                'trainings.browse.options',
                '/trainings',
                fn() => new PreflightAction()
            )
            ->get(
                'trainings.browse',
                '/trainings',
                fn(ContainerInterface $container) => new BrowseTrainingsAction($container)
            )
            ->options(
                'trainings.get.options',
                '/trainings/{id}',
                fn() => new PreflightAction(),
                    requirements: [
                        'id' => '\d+'
                    ]
            )
            ->get(
                'trainings.get',
                '/trainings/{id}',
                fn(ContainerInterface $container) => new GetTrainingAction($container),
                requirements: [
                    'id' => '\d+'
                ]
            )
        ;

        $definitionRouters = new Router();
        $definitionRouters
            ->options(
                'trainings.definitions.browse.options',
                '/definitions',
                fn() => new PreflightAction()
            )
            ->get(
                'trainings.definitions.browse',
                '/definitions',
                fn(ContainerInterface $container) => new BrowseDefinitionsAction($container)
            )
            ->options(
                'trainings.definitions.get.options',
                '/definitions/{id}',
                fn() => new PreflightAction(),
                requirements: [
                    'id' => '\d+'
                ]
            )
            ->get(
                'trainings.definitions.get',
                '/definitions/{id}',
                fn(ContainerInterface $container) => new GetDefinitionAction($container),
                requirements: [
                    'id' => '\d+'
                ]
            )
        ;
        $router->group('/trainings', $definitionRouters);

        return $router;
    }

    protected function addDependencies(): void
    {
        parent::addDependencies();
        $this->addDependency('converter', new ConvertDependency());
    }
}
