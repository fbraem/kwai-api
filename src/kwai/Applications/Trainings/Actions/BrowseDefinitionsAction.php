<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Applications\Trainings\Actions;

use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Dependencies\DatabaseDependency;
use Kwai\Core\Infrastructure\Presentation\Action;
use Kwai\Core\Infrastructure\Presentation\Responses\JSONAPIResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\SimpleResponse;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\JSONAPI;
use Kwai\Modules\Trainings\Infrastructure\Repositories\DefinitionDatabaseRepository;
use Kwai\Modules\Trainings\Presentation\Resources\DefinitionResource;
use Kwai\Modules\Trainings\UseCases\BrowseDefinitions;
use Kwai\Modules\Trainings\UseCases\BrowseDefinitionsCommand;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use function depends;

/**
 * Class BrowseDefinitionsAction
 */
class BrowseDefinitionsAction extends Action
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
        $command = new BrowseDefinitionsCommand();

        $parameters = $request->getAttribute('parameters');

        if (isset($parameters['page']['limit'])) {
            $command->limit = (int) $parameters['page']['limit'];
        }
        if (isset($parameters['page']['offset'])) {
            $command->offset = (int)$parameters['page']['offset'];
        }

        try {
            [$count, $definitions] = BrowseDefinitions::create(
                new DefinitionDatabaseRepository($this->database)
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

        $resources = $definitions->map(
            fn($definition) => new DefinitionResource($definition)
        );

        $meta = [
            'count' => $count
        ];
        if ($command->limit) {
            $meta['limit'] = $command->limit;
            $meta['offset'] = $command->offset;
        }
        return (new JSONAPIResponse(
            JSONAPI\Document::createFromArray($resources->toArray())
                ->setMeta($meta)
        ))($response);
    }
}
