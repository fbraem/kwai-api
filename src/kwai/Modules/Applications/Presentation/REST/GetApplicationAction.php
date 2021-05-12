<?php
/**
 * @package Modules
 * @subpackage Applications
 */
declare(strict_types=1);

namespace Kwai\Modules\Applications\Presentation\REST;

use Kwai\Core\Infrastructure\Presentation\Action;
use Kwai\Core\Infrastructure\Presentation\Responses\NotFoundResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\ResourceResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\SimpleResponse;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Applications\Domain\Exceptions\ApplicationNotFoundException;
use Kwai\Modules\Applications\Infrastructure\Repositories\ApplicationDatabaseRepository;
use Kwai\Modules\Applications\Presentation\Transformers\ApplicationTransformer;
use Kwai\Modules\Applications\UseCases\GetApplication;
use Kwai\Modules\Applications\UseCases\GetApplicationCommand;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class GetApplicationAction extends Action
{
    public function __invoke(Request $request, Response $response, array $args)
    {
        $command = new GetApplicationCommand();
        $command->id = (int) $args['id'];

        $database = $this->getContainerEntry('pdo_db');

        try {
            $application = GetApplication::create(
                new ApplicationDatabaseRepository($database)
            )($command);
        } catch (ApplicationNotFoundException) {
            return (new NotFoundResponse('Application not found'))($response);
        } catch (RepositoryException $e) {
            $this->logException($e);
            return (new SimpleResponse(
                500,
                'A repository exception occurred'
            ))($response);
        }

        $resource = ApplicationTransformer::createForItem($application);
        return (new ResourceResponse(
            $resource
        ))($response);
    }
}
