<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Presentation\REST;

use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Presentation\Action;
use Kwai\Core\Infrastructure\Presentation\Responses\NotFoundResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\ResourceResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\SimpleResponse;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Trainings\Domain\Exceptions\CoachNotFoundException;
use Kwai\Modules\Trainings\Infrastructure\Repositories\CoachDatabaseRepository;
use Kwai\Modules\Trainings\Infrastructure\Repositories\TrainingDatabaseRepository;
use Kwai\Modules\Trainings\Presentation\Transformers\TrainingTransformer;
use Kwai\Modules\Trainings\UseCases\BrowseTrainings;
use Kwai\Modules\Trainings\UseCases\BrowseTrainingsCommand;
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
        $command = new BrowseTrainingsCommand();

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
            [$count, $trainings] = BrowseTrainings::create(
                new TrainingDatabaseRepository($db),
                new CoachDatabaseRepository($db)
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
        } catch (CoachNotFoundException) {
            return (new NotFoundResponse('Coach not found'))($response);
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
