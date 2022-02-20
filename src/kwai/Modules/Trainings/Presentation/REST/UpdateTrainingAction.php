<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Presentation\REST;

use Kwai\Core\Domain\ValueObjects\Creator;
use Kwai\Core\Infrastructure\Converter\ConverterFactory;
use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Dependencies\ConvertDependency;
use Kwai\Core\Infrastructure\Dependencies\DatabaseDependency;
use Kwai\Core\Infrastructure\Presentation\Action;
use Kwai\Core\Infrastructure\Presentation\InputSchemaProcessor;
use Kwai\Core\Infrastructure\Presentation\Responses\JSONAPIResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\NotFoundResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\SimpleResponse;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\JSONAPI;
use Kwai\Modules\Trainings\Domain\Exceptions\DefinitionNotFoundException;
use Kwai\Modules\Trainings\Domain\Exceptions\TrainingNotFoundException;
use Kwai\Modules\Trainings\Infrastructure\Repositories\CoachDatabaseRepository;
use Kwai\Modules\Trainings\Infrastructure\Repositories\DefinitionDatabaseRepository;
use Kwai\Modules\Trainings\Infrastructure\Repositories\TeamDatabaseRepository;
use Kwai\Modules\Trainings\Infrastructure\Repositories\TrainingDatabaseRepository;
use Kwai\Modules\Trainings\Presentation\Resources\TrainingResource;
use Kwai\Modules\Trainings\UseCases\UpdateTraining;
use Nette\Schema\ValidationException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;

/**
 * Class UpdateTrainingAction
 */
class UpdateTrainingAction extends Action
{
    public function __construct(
        ?LoggerInterface $logger = null,
        private ?Connection $database = null,
        private ?ConverterFactory $converterFactory = null
    ) {
        parent::__construct($logger);
        $this->database ??= depends('kwai.database', DatabaseDependency::class);
        $this->converterFactory ??= depends('kwai.converter', ConvertDependency::class);
    }

    /**
     * @inheritDoc
     */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        try {
            $command = InputSchemaProcessor::create(new TrainingSchema())
                ->process($request->getParsedBody());
        } catch (ValidationException $ve) {
            return (new SimpleResponse(422, $ve->getMessage()))($response);
        }

        if ($command->id != (int) $args['id']) {
            return (new SimpleResponse(400, 'id in body and url should be the same.'))($response);
        }

        $user = $request->getAttribute('kwai.user');
        $creator = new Creator($user->id(), $user->getUsername());

        try {
            $training = UpdateTraining::create(
                new TrainingDatabaseRepository($this->database),
                new DefinitionDatabaseRepository($this->database),
                new TeamDatabaseRepository($this->database),
                new CoachDatabaseRepository($this->database)
            )($command, $creator);
        } catch (RepositoryException $e) {
            $this->logException($e);
            return (
                new SimpleResponse(500, 'A repository exception occurred.')
            )($response);
        } catch (DefinitionNotFoundException) {
            return (new NotFoundResponse('Definition not found'))($response);
        } catch (TrainingNotFoundException) {
            return (new NotFoundResponse('Training not found'))($response);
        }

        $resource = new TrainingResource(
            $training,
            $this->converterFactory
        );

        return (new JSONAPIResponse(
            JSONAPI\Document::createFromObject($resource)
        ))($response);
    }
}
