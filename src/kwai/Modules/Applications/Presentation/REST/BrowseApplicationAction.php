<?php
/**
 * @package Modules
 * @subpackage Applications
 */
declare(strict_types=1);

namespace Kwai\Modules\Applications\Presentation\REST;

use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Presentation\Action;
use Kwai\Core\Infrastructure\Presentation\Responses\ResourceResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\SimpleResponse;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Applications\Infrastructure\Repositories\ApplicationDatabaseRepository;
use Kwai\Modules\Applications\Presentation\Transformers\ApplicationTransformer;
use Kwai\Modules\Applications\UseCases\BrowseApplication;
use Kwai\Modules\Applications\UseCases\BrowseApplicationCommand;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class BrowseApplicationAction
 */
class BrowseApplicationAction extends Action
{
    /**
     * @inheritDoc
     */
    public function __invoke(Request $request, Response $response, array $args)
    {
        $command = new BrowseApplicationCommand();
        $database = $this->getContainerEntry('pdo_db');

        try {
            [$count, $applications] = BrowseApplication::create(
                new ApplicationDatabaseRepository($database)
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

        $resource = ApplicationTransformer::createForCollection(
            $applications
        );
        $resource->setMeta([
            'limit' => 0,
            'offset' => 0,
            'count' => $count
        ]);
        return (new ResourceResponse(
            $resource
        ))($response);
    }
}
