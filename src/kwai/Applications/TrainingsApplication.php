<?php
/**
 * @package Applications
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Applications;

use Kwai\Modules\Trainings\Presentation\REST\BrowseDefinitionsAction;
use Kwai\Modules\Trainings\Presentation\REST\BrowseTrainingsAction;
use Kwai\Modules\Trainings\Presentation\REST\CreateDefinitionAction;
use Kwai\Modules\Trainings\Presentation\REST\GetDefinitionAction;
use Kwai\Modules\Trainings\Presentation\REST\GetTrainingAction;
use Kwai\Modules\Trainings\Presentation\REST\GetTrainingPresencesAction;
use Kwai\Core\Infrastructure\Presentation\Router;
use Kwai\Modules\Trainings\Presentation\REST\UpdateDefinitionAction;

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
            ->get(
                'trainings.get',
                '/trainings/{id}',
                GetTrainingAction::class,
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
