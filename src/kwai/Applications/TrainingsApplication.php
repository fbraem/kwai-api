<?php
/**
 * @package Applications
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Applications;

use Kwai\Modules\Trainings\Presentation\REST\BrowseDefinitionsAction;
use Kwai\Modules\Trainings\Presentation\REST\BrowseTrainingsAction;
use Kwai\Modules\Trainings\Presentation\REST\GetDefinitionAction;
use Kwai\Modules\Trainings\Presentation\REST\GetTrainingAction;
use Kwai\Modules\Trainings\Presentation\REST\GetTrainingPresencesAction;
use Kwai\Core\Infrastructure\Dependencies\ConvertDependency;
use Kwai\Core\Infrastructure\Presentation\Router;
use Psr\Container\ContainerInterface;

/**
 * Class TrainingsApplication
 *
 * Public application for trainings.
 */
class TrainingsApplication extends Application
{
    public function createRouter(): Router
    {
        $router = new Router();

        $router
            ->get(
                'trainings.browse',
                '/trainings',
                fn(ContainerInterface $container) => new BrowseTrainingsAction($container)
            )
            ->get(
                'trainings.get',
                '/trainings/{id}',
                fn(ContainerInterface $container) => new GetTrainingAction($container),
                requirements: [
                    'id' => '\d+'
                ]
            )
            ->get(
                'trainings.get.presences',
                '/trainings/{id}/presences',
                fn(ContainerInterface $container) => new GetTrainingPresencesAction($container),
                requirements: [
                    'id' => '\d+'
                ]
            )
        ;

        $definitionRouters = new Router();
        $definitionRouters
            ->get(
                'trainings.definitions.browse',
                '/definitions',
                fn(ContainerInterface $container) => new BrowseDefinitionsAction($container)
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
