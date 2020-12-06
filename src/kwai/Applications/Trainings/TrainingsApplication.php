<?php
/**
 * @package Applications
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Applications\Trainings;

use Kwai\Applications\Application;
use Kwai\Applications\Trainings\Actions\BrowseDefinitionsAction;
use Kwai\Applications\Trainings\Actions\BrowseTrainingsAction;
use Kwai\Applications\Trainings\Actions\GetDefinitionAction;
use Kwai\Applications\Trainings\Actions\GetTrainingAction;
use Kwai\Core\Infrastructure\Dependencies\ConvertDependency;
use Kwai\Core\Infrastructure\Presentation\PreflightAction;
use Slim\Routing\RouteCollectorProxy;

/**
 * Class TrainingsApplication
 *
 * Public application for trainings.
 */
class TrainingsApplication extends Application
{
    /**
     * TrainingsApplication constructor.
     */
    public function __construct()
    {
        parent::__construct('trainings');
    }

    public function createRoutes(RouteCollectorProxy $group): void
    {
        $group->group(
            '',
            function (RouteCollectorProxy $trainingsGroup) {
                $trainingsGroup
                    ->options('', PreflightAction::class)
                ;
                $trainingsGroup
                    ->get('', BrowseTrainingsAction::class)
                    ->setName('trainings.browse')
                ;
            }
        );
        $group->group(
            '/{id:[0-9]+}',
            function (RouteCollectorProxy $trainingGroup) {
                $trainingGroup
                    ->options('', PreflightAction::class)
                ;
                $trainingGroup
                    ->get('', GetTrainingAction::class)
                    ->setName('trainings.get')
                ;
            }
        );
        $group->group(
            '/definitions',
            function(RouteCollectorProxy $definitionGroup) {
                $definitionGroup
                    ->options('', PreflightAction::class)
                ;
                $definitionGroup
                    ->get('', BrowseDefinitionsAction::class)
                    ->setName('trainings.definitions.browse')
                ;

            }
        );
        $group->group(
            '/definitions/{id:[0-9]+}',
            function(RouteCollectorProxy $definitionGroup) {
                $definitionGroup
                    ->options('', PreflightAction::class)
                ;
                $definitionGroup
                    ->get('', GetDefinitionAction::class)
                    ->setName('trainings.definitions.get')
                ;

            }
        );
    }

    public function addDependencies(): void
    {
        parent::addDependencies();

        $this->addDependency('converter', new ConvertDependency());
    }
}
