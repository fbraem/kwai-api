<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Applications\Trainings\Actions;

use Kwai\Applications\Trainings\Resources\PresenceResource;
use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Dependencies\DatabaseDependency;
use Kwai\Core\Infrastructure\Presentation\Action;
use Kwai\Core\Infrastructure\Presentation\Responses\JSONAPIResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\NotFoundResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\SimpleResponse;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\JSONAPI;
use Kwai\Modules\Trainings\Domain\Exceptions\TrainingNotFoundException;
use Kwai\Modules\Trainings\Domain\ValueObjects\Presence;
use Kwai\Modules\Trainings\Infrastructure\Repositories\TrainingDatabaseRepository;
use Kwai\Modules\Trainings\UseCases\GetTraining;
use Kwai\Modules\Trainings\UseCases\GetTrainingCommand;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use function depends;

/**
 * Class GetTrainingPresencesAction
 */
class GetTrainingPresencesAction extends Action
{
    public function __construct(
        private ?Connection $database = null
    ) {
        parent::__construct();
        $this->database ??= depends('kwai.database', DatabaseDependency::class);
    }

    /**
     * @inheritDoc
     */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $command = new GetTrainingCommand();
        $command->id = (int) $args['id'];
        $command->withPresences = true;

        try {
            $training = GetTraining::create(
                new TrainingDatabaseRepository($this->database)
            )($command);
        } catch (RepositoryException $re) {
            $this->logException($re);
            return (
            new SimpleResponse(500, 'A repository exception occurred.')
            )($response);
        } catch (TrainingNotFoundException) {
            return (new NotFoundResponse('Training not found'))($response);
        }

        $resources = $training->getPresences()->map(
            fn (Presence $presence) => new PresenceResource($presence)
        );

        return (new JSONAPIResponse(
            JSONAPI\Document::createFromArray($resources->toArray())
        ))($response);
    }
}
