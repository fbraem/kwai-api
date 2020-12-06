<?php
/**
 * @package Applications
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Applications\Trainings\Actions;

use Kwai\Core\Infrastructure\Presentation\Action;
use Kwai\Core\Infrastructure\Presentation\Responses\NotFoundResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\ResourceResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\SimpleResponse;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Trainings\Domain\Exceptions\TrainingDefinitionNotFoundException;
use Kwai\Modules\Trainings\Infrastructure\Repositories\DefinitionDatabaseRepository;
use Kwai\Modules\Trainings\Presentation\Transformers\DefinitionTransformer;
use Kwai\Modules\Trainings\UseCases\GetDefinition;
use Kwai\Modules\Trainings\UseCases\GetDefinitionCommand;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class GetDefinitionAction
 */
class GetDefinitionAction extends Action
{
    /**
     * @inheritDoc
     */
    public function __invoke(Request $request, Response $response, array $args)
    {
        $command = new GetDefinitionCommand();
        $command->id = (int) $args['id'];

        $database = $this->getContainerEntry('pdo_db');

        try {
            $definition = GetDefinition::create(
                new DefinitionDatabaseRepository($database)
            )($command);
        } catch (RepositoryException $re) {
            $this->logException($re);
            return (
                new SimpleResponse(500, 'A repository exception occurred.')
            )($response);
        } catch (TrainingDefinitionNotFoundException) {
            return (new NotFoundResponse('Training definition not found'))($response);
        }

        $resource = DefinitionTransformer::createForItem($definition);

        return (new ResourceResponse(
            $resource
        ))($response);
    }
}
