<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Presentation\REST;

use Kwai\Core\Domain\ValueObjects\Creator;
use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Dependencies\DatabaseDependency;
use Kwai\Core\Infrastructure\Presentation\Action;
use Kwai\Core\Infrastructure\Presentation\InputSchemaProcessor;
use Kwai\Core\Infrastructure\Presentation\Responses\JSONAPIResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\NotFoundResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\SimpleResponse;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\JSONAPI;
use Kwai\Modules\Trainings\Domain\Exceptions\DefinitionNotFoundException;
use Kwai\Modules\Trainings\Domain\Exceptions\SeasonNotFoundException;
use Kwai\Modules\Trainings\Domain\Exceptions\TeamNotFoundException;
use Kwai\Modules\Trainings\Infrastructure\Repositories\DefinitionDatabaseRepository;
use Kwai\Modules\Trainings\Infrastructure\Repositories\SeasonDatabaseRepository;
use Kwai\Modules\Trainings\Infrastructure\Repositories\TeamDatabaseRepository;
use Kwai\Modules\Trainings\Presentation\Resources\DefinitionResource;
use Kwai\Modules\Trainings\UseCases\UpdateDefinition;
use Nette\Schema\ValidationException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;

/**
 * Class UpdateDefinitionAction
 */
class UpdateDefinitionAction extends Action
{
    public function __construct(
        ?LoggerInterface $logger = null,
        private ?Connection $database = null
    ) {
        parent::__construct($logger);
        $this->database ??= depends('kwai.database', DatabaseDependency::class);
    }

    /**
     * @inheritDoc
     */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        try {
            $command = InputSchemaProcessor::create(new DefinitionSchema())
                ->process($request->getParsedBody())
            ;
        } catch (ValidationException $ve) {
            return (new SimpleResponse(422, $ve->getMessage()))($response);
        }

        if ($command->id != (int) $args['id']) {
            return (new SimpleResponse(400, 'id in body and url should be the same.'))($response);
        }

        $user = $request->getAttribute('kwai.user');
        $creator = new Creator($user->id(), $user->getUsername());

        try {
            $definition = UpdateDefinition::create(
                new DefinitionDatabaseRepository($this->database),
                new TeamDatabaseRepository($this->database),
                new SeasonDatabaseRepository($this->database)
            )($command, $creator);
        } catch (RepositoryException $e) {
            $this->logException($e);
            return (
                new SimpleResponse(500, 'A repository exception occurred.')
            )($response);
        } catch (DefinitionNotFoundException) {
            return (new NotFoundResponse('Definition not found'))($response);
        } catch (SeasonNotFoundException) {
            return (new NotFoundResponse('Season not found'))($response);
        } catch (TeamNotFoundException) {
            return (new NotFoundResponse('Team not found'))($response);
        }

        $resource = new DefinitionResource($definition);

        return (new JSONAPIResponse(
            JSONAPI\Document::createFromObject($resource)
        ))($response);
    }
}
