<?php
/**
 * @package Applications
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Applications;

use Kwai\Applications\Trainings\Actions\BrowseDefinitionsAction;
use Kwai\Applications\Trainings\Actions\BrowseTrainingsAction;
use Kwai\Applications\Trainings\Actions\CreateDefinitionAction;
use Kwai\Applications\Trainings\Actions\CreateTrainingAction;
use Kwai\Applications\Trainings\Actions\GetDefinitionAction;
use Kwai\Applications\Trainings\Actions\GetTrainingAction;
use Kwai\Applications\Trainings\Actions\GetTrainingPresencesAction;
use Kwai\Applications\Trainings\Actions\UpdateDefinitionAction;
use Kwai\Applications\Trainings\Actions\UpdateTrainingAction;
use Kwai\Core\Infrastructure\Presentation\Router;

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
                BrowseTrainingsAction::class
            )
            ->post(
                'trainings.create',
                '/trainings',
                CreateTrainingAction::class,
                [
                    'auth' => true
                ]
            )
            ->get(
                'trainings.get',
                '/trainings/{id}',
                GetTrainingAction::class,
                requirements: [
                    'id' => '\d+'
                ]
            )
            ->patch(
                'trainings.update',
                '/trainings/{id}',
                UpdateTrainingAction::class,
                [
                    'auth' => true
                ],
                requirements: [
                    'id' => '\d+'
                ]
            )
            ->get(
                'trainings.get.presences',
                '/trainings/{id}/presences',
                GetTrainingPresencesAction::class,
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
                BrowseDefinitionsAction::class
            )
            ->get(
                'trainings.definitions.get',
                '/definitions/{id}',
                GetDefinitionAction::class,
                requirements: [
                    'id' => '\d+'
                ]
            )
            ->post(
                'training.definitions.create',
                '/definitions',
                CreateDefinitionAction::class,
                [
                    'auth' => true
                ]
            )
            ->patch(
                'trainings.definition.update',
                '/definitions/{id}',
                UpdateDefinitionAction::class,
                [
                    'auth' => true
                ],
                [
                    'id' => '\d+'
                ]
            )
        ;
        $router->group('/trainings', $definitionRouters);

        return $router;
    }
}
