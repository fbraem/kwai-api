<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Presentation\REST;

use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Dependencies\DatabaseDependency;
use Kwai\Core\Infrastructure\Presentation\Action;
use Kwai\Core\Infrastructure\Presentation\Responses\JSONAPIResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\NotFoundResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\SimpleResponse;
use Kwai\JSONAPI;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Trainings\Domain\Exceptions\DefinitionNotFoundException;
use Kwai\Modules\Trainings\Infrastructure\Repositories\DefinitionDatabaseRepository;
use Kwai\Modules\Trainings\Presentation\Resources\DefinitionResource;
use Kwai\Modules\Trainings\UseCases\GetDefinition;
use Kwai\Modules\Trainings\UseCases\GetDefinitionCommand;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class GetDefinitionAction
 */
class GetDefinitionAction extends Action
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
        $command = new GetDefinitionCommand();
        $command->id = (int) $args['id'];

        try {
            $definition = GetDefinition::create(
                new DefinitionDatabaseRepository($this->database)
            )($command);
        } catch (RepositoryException $re) {
            $this->logException($re);
            return (
                new SimpleResponse(500, 'A repository exception occurred.')
            )($response);
        } catch (DefinitionNotFoundException) {
            return (new NotFoundResponse('Training definition not found'))($response);
        }

        $resource = new DefinitionResource($definition);

        return (new JSONAPIResponse(
            JSONAPI\Document::createFromObject($resource)
        ))($response);
    }
}
