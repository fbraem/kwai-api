<?php
/**
 * @package Modules
 * @subpackage Applications
 */
declare(strict_types=1);

namespace Kwai\Modules\Applications\Presentation\REST;

use Kwai\Core\Domain\ValueObjects\Creator;
use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Dependencies\DatabaseDependency;
use Kwai\Core\Infrastructure\Presentation\Action;
use Kwai\Core\Infrastructure\Presentation\Responses\JSONAPIResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\NotFoundResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\SimpleResponse;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Applications\Domain\Exceptions\ApplicationNotFoundException;
use Kwai\Modules\Applications\Infrastructure\Repositories\ApplicationDatabaseRepository;
use Kwai\Modules\Applications\Presentation\Resources\ApplicationResource;
use Kwai\Modules\Applications\UseCases\UpdateApplication;
use Kwai\Modules\Applications\UseCases\UpdateApplicationCommand;
use Kwai\JSONAPI;
use Nette\Schema\Elements\Structure;
use Nette\Schema\Expect;
use Nette\Schema\Processor;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class UpdateApplicationAction
 */
class UpdateApplicationAction extends Action
{
    public function __construct(
        private ?Connection $database = null
    ) {
        parent::__construct();
        $this->database ??= depends('kwai.database', DatabaseDependency::class);
    }

    protected function createSchema(): Structure
    {
        return Expect::structure([
           'data' => Expect::structure([
               'type' => Expect::string(),
               'id' => Expect::string(),
               'attributes' => Expect::structure([
                   'title' => Expect::string()->required(),
                   'short_description' => Expect::string()->required(),
                   'description' => Expect::string()->required()
               ])->required()
           ])
        ]);
    }

    protected function processInput($data): UpdateApplicationCommand
    {
        $processor = new Processor();
        $normalized = $processor->process($this->createSchema(), $data);
        $command = new UpdateApplicationCommand();
        $command->title = $normalized->data->attributes->title;
        $command->short_description = $normalized->data->attributes->short_description;
        $command->description = $normalized->data->attributes->description;
        $command->id = (int) $normalized->data->id;
        return $command;
    }

    public function __invoke(Request $request, Response $response, array $args)
    {
        $command = $this->processInput($request->getParsedBody());

        $user = $request->getAttribute('kwai.user');
        $creator = new Creator($user->id(), $user->getUsername());

        try {
            $application = UpdateApplication::create(
                new ApplicationDatabaseRepository($this->database)
            )($command, $creator);
        } catch (RepositoryException $e) {
            $this->logException($e);
            return (new SimpleResponse(
                500,
                'A repository exception occurred'
            ))($response);
        } catch (ApplicationNotFoundException) {
            return (new NotFoundResponse('Application not found'))($response);
        } catch (QueryException $e) {
            $this->logException($e);
            return (new SimpleResponse(
                500,
                'A query exception occurred'
            ))($response);
        }

        return (new JSONAPIResponse(
            JSONAPI\Document::createFromObject(
                new ApplicationResource($application)
            )
        ))($response);
    }
}
