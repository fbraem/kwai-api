<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Applications\Trainings\Actions;

use Kwai\Applications\Trainings\Resources\TrainingResource;
use Kwai\Core\Infrastructure\Converter\ConverterFactory;
use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Dependencies\ConvertDependency;
use Kwai\Core\Infrastructure\Dependencies\DatabaseDependency;
use Kwai\Core\Infrastructure\Presentation\Action;
use Kwai\Core\Infrastructure\Presentation\Responses\JSONAPIResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\NotFoundResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\SimpleResponse;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\JSONAPI;
use Kwai\Modules\Trainings\Domain\Exceptions\CoachNotFoundException;
use Kwai\Modules\Trainings\Domain\Exceptions\DefinitionNotFoundException;
use Kwai\Modules\Trainings\Infrastructure\Repositories\CoachDatabaseRepository;
use Kwai\Modules\Trainings\Infrastructure\Repositories\DefinitionDatabaseRepository;
use Kwai\Modules\Trainings\Infrastructure\Repositories\TrainingDatabaseRepository;
use Kwai\Modules\Trainings\UseCases\BrowseTrainings;
use Kwai\Modules\Trainings\UseCases\BrowseTrainingsCommand;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use function depends;

/**
 * Class BrowseTrainingsAction
 */
class BrowseTrainingsAction extends Action
{
    public function __construct(
        private ?Connection $database = null,
        private ?ConverterFactory $converterFactory = null
    ) {
        parent::__construct();
        $this->database ??= depends('kwai.database', DatabaseDependency::class);
        $this->converterFactory ??= depends('kwai.converter', ConvertDependency::class);
    }

    /**
     * @inheritDoc
     */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $command = new BrowseTrainingsCommand();

        $parameters = $request->getAttribute('parameters');
        if (isset($parameters['filter']['year'])) {
            $command->year = (int) $parameters['filter']['year'];
        }
        if (isset($parameters['filter']['month'])) {
            $command->month = (int) $parameters['filter']['month'];
        }
        if (isset($parameters['filter']['week'])) {
            $command->week = (int) $parameters['filter']['week'];
        }
        $command->start = $parameters['filter']['start'] ?? null;
        $command->end = $parameters['filter']['end'] ?? null;

        if (isset($parameters['filter']['coach'])) {
            $command->coach = (int) $parameters['filter']['coach'];
        }

        if (isset($parameters['filter']['definition'])) {
            $command->definition = (int) $parameters['filter']['definition'];
        }

        if (isset($parameters['filter']['active'])) {
            $command->active = (bool) $parameters['filter']['active'];
        }

        if (isset($parameters['page']['limit'])) {
            $command->limit = (int)($parameters['page']['limit'] ?? 10);
        }
        if (isset($parameters['page']['offset'])) {
            $command->offset = (int)($parameters['page']['offset'] ?? 0);
        }

        try {
            [$count, $trainings] = BrowseTrainings::create(
                new TrainingDatabaseRepository($this->database),
                new CoachDatabaseRepository($this->database),
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
        } catch (CoachNotFoundException) {
            return (new NotFoundResponse('Coach not found'))($response);
        } catch (DefinitionNotFoundException $e) {
            return (new NotFoundResponse('Definition not found'))($response);
        }

        $resources = $trainings->map(
            fn ($training) => new TrainingResource(
                $training,
                $this->converterFactory
            )
        );

        $meta = [
            'count' => $count
        ];
        if ($command->limit) {
            $meta['limit'] = $command->limit;
            $meta['offset'] = $command->offset ?? 0;
        }

        return (new JSONAPIResponse(
            JSONAPI\Document::createFromArray($resources->toArray())
                ->setMeta($meta)
        ))($response);
    }
}
