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
use Kwai\Modules\Trainings\Infrastructure\Repositories\TrainingDatabaseRepository;
use Kwai\Modules\Trainings\Presentation\Transformers\TrainingTransformer;
use Kwai\Modules\Trainings\UseCases\BrowseTraining;
use Kwai\Modules\Trainings\UseCases\BrowseTrainingCommand;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class BrowseTrainingsAction
 */
class BrowseTrainingsAction extends Action
{
    /**
     * @inheritDoc
     */
    public function __invoke(Request $request, Response $response, array $args)
    {
        $command = new BrowseTrainingCommand();

        $parameters = $request->getAttribute('parameters');
        if (isset($parameters['filter']['year'])) {
            $command->year = (int) $parameters['filter']['year'];
        }
        if (isset($parameters['filter']['month'])) {
            $command->month = (int) $parameters['filter']['month'];
        }

        if (! isset($command->year)) {
            $command->limit = (int)($parameters['page']['limit'] ?? 10);
            $command->offset = (int)($parameters['page']['offset'] ?? 0);
        }

        $db = $this->getContainerEntry('pdo_db');

        try {
            [$count, $trainings] = BrowseTraining::create(
                new TrainingDatabaseRepository($db)
            )($command);
        } catch (QueryException $e) {
            $this->logException($e);
            return (
                new SimpleResponse(500, 'A query exception occurred.')
            )($response);
        }

        $resource = TrainingTransformer::createForCollection(
            $trainings,
            $this->getContainerEntry('converter')
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
