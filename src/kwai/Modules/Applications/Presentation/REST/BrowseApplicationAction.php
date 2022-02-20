<?php
/**
 * @package Modules
 * @subpackage Applications
 */
declare(strict_types=1);

namespace Kwai\Modules\Applications\Presentation\REST;

use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Dependencies\DatabaseDependency;
use Kwai\Core\Infrastructure\Presentation\Action;
use Kwai\Core\Infrastructure\Presentation\Responses\JSONAPIResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\SimpleResponse;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\JSONAPI;
use Kwai\Modules\Applications\Infrastructure\Repositories\ApplicationDatabaseRepository;
use Kwai\Modules\Applications\Presentation\Resources\ApplicationResource;
use Kwai\Modules\Applications\UseCases\BrowseApplication;
use Kwai\Modules\Applications\UseCases\BrowseApplicationCommand;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class BrowseApplicationAction
 */
class BrowseApplicationAction extends Action
{
    public function __construct(
        private ?Connection $database = null
    ) {
        parent::__construct();
        $this->database = depends('kwai.database', DatabaseDependency::class);
    }

    /**
     * @inheritDoc
     */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $command = new BrowseApplicationCommand();

        try {
            [$count, $applications] = BrowseApplication::create(
                new ApplicationDatabaseRepository($this->database)
            )($command);
        } catch (QueryException $e) {
            $this->logException($e);
            return (new SimpleResponse(
                500,
                'A query exception occurred'
            ))($response);
        } catch (RepositoryException $e) {
            $this->logException($e);
            return (new SimpleResponse(
                500,
                'A repository exception occurred'
            ))($response);
        }

        $resources = $applications->map(
            fn($application) => new ApplicationResource($application)
        );

        return (new JSONAPIResponse(
            JSONAPI\Document::createFromArray($resources->toArray())
            ->setMeta([
                'limit' => 0,
                'offset' => 0,
                'count' => $count
            ])
        ))($response);
    }
}
