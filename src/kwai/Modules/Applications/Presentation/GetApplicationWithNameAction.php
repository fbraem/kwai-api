<?php
/**
 * @package Modules
 * @subpackage Application
 */
declare(strict_types=1);

namespace Kwai\Modules\Applications\Presentation;

use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Presentation\Action;
use Kwai\Core\Infrastructure\Presentation\Responses\NotFoundResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\ResourceResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\SimpleResponse;
use Kwai\Modules\Applications\Infrastructure\Repositories\ApplicationDatabaseRepository;
use Kwai\Modules\Applications\Presentation\Transformers\ApplicationTransformer;
use Kwai\Modules\Applications\UseCases\BrowseApplication;
use Kwai\Modules\Applications\UseCases\BrowseApplicationCommand;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class GetApplicationWithNameAction
 *
 * Get the application with the given name.
 */
class GetApplicationWithNameAction extends Action
{
    /**
     * @inheritDoc
     */
    public function __invoke(Request $request, Response $response, array $args)
    {
        $command = new BrowseApplicationCommand();
        $command->app = $args['application'];

        $database = $this->getContainerEntry('pdo_db');
        $repo = new ApplicationDatabaseRepository($database);
        try {
            [$count, $applications] = (new BrowseApplication($repo))($command);
        } catch (QueryException $e) {
            return (new SimpleResponse(
                500,
                'A query exception occurred'
            ))($response);
        }

        if ($count() == 0) {
            return (new NotFoundResponse('Application not found'))($response);
        }

        return (new ResourceResponse(
            ApplicationTransformer::createForItem(
                $applications->first()
            )
        ))($response);
    }
}
