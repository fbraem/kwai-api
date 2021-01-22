<?php
/**
 * @package Application
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Applications\Trainings\Actions;

use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Presentation\Action;
use Kwai\Core\Infrastructure\Presentation\Responses\ResourceResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\SimpleResponse;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Trainings\Infrastructure\Repositories\DefinitionDatabaseRepository;
use Kwai\Modules\Trainings\Presentation\Transformers\DefinitionTransformer;
use Kwai\Modules\Trainings\UseCases\BrowseDefinitions;
use Kwai\Modules\Trainings\UseCases\BrowseDefinitionsCommand;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class BrowseDefinitionsAction
 */
class BrowseDefinitionsAction extends Action
{
    /**
     * @inheritDoc
     */
    public function __invoke(Request $request, Response $response, array $args)
    {
        $command = new BrowseDefinitionsCommand();

        $parameters = $request->getAttribute('parameters');

        if (isset($parameters['page']['limit'])) {
            $command->limit = (int) $parameters['page']['limit'];
        }
        if (isset($parameters['page']['offset'])) {
            $command->offset = (int)$parameters['page']['offset'];
        }

        $db = $this->getContainerEntry('pdo_db');

        try {
            [$count, $definitions] = BrowseDefinitions::create(
                new DefinitionDatabaseRepository($db)
            )($command);
        } catch (QueryException $e) {
            $this->logException($e);
            return (
                new SimpleResponse(500, 'A query exception occurred.')
            )($response);
        } catch (RepositoryException $e) {
            $this->logException($e);
            return (
                new SimpleResponse(500, 'A repository exception occurred.')
            )($response);
        }

        $resource = DefinitionTransformer::createForCollection(
            $definitions
        );

        $meta = [
            'count' => $count
        ];
        if ($command->limit) {
            $meta['limit'] = $command->limit;
            $meta['offset'] = $command->offset;
        }
        $resource->setMeta($meta);

        return (new ResourceResponse(
            $resource
        ))($response);
    }
}
