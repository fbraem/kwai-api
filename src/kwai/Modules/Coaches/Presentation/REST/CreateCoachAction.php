<?php
/**
 * @package Modules
 * @subpackage Coaches
 */
declare(strict_types=1);

namespace Kwai\Modules\Coaches\Presentation\REST;

use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Dependencies\DatabaseDependency;
use Kwai\Core\Infrastructure\Presentation\Action;
use Kwai\Core\Infrastructure\Presentation\InputSchemaProcessor;
use Kwai\Core\Infrastructure\Presentation\Responses\JSONAPIResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\NotFoundResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\SimpleResponse;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\JSONAPI;
use Kwai\Modules\Coaches\Domain\Exceptions\CoachAlreadyExistsException;
use Kwai\Modules\Coaches\Domain\Exceptions\MemberNotFoundException;
use Kwai\Modules\Coaches\Domain\Exceptions\UserNotFoundException;
use Kwai\Modules\Coaches\Infrastructure\Repositories\CoachDatabaseRepository;
use Kwai\Modules\Coaches\Infrastructure\Repositories\MemberDatabaseRepository;
use Kwai\Modules\Coaches\Infrastructure\Repositories\UserDatabaseRepository;
use Kwai\Modules\Coaches\Presentation\Resources\CoachResource;
use Kwai\Modules\Coaches\UseCases\CreateCoach;
use Nette\Schema\ValidationException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class CreateCoachAction
 */
class CreateCoachAction extends Action
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
        try {
            $command = InputSchemaProcessor::create(new CoachSchema(true))
                ->process($request->getParsedBody());
        } catch (ValidationException $ve) {
            return (new SimpleResponse(422, $ve->getMessage()))($response);
        }

        try {
            $coach = CreateCoach::create(
                new CoachDatabaseRepository($this->database),
                new MemberDatabaseRepository($this->database),
                new UserDatabaseRepository($this->database)
            )($command);
        } catch (RepositoryException $e) {
            $this->logException($e);
            return (
                new SimpleResponse(500, 'A repository exception occurred.')
            )($response);
        } catch (UserNotFoundException) {
            return (new NotFoundResponse('User not found'))($response);
        } catch (CoachAlreadyExistsException) {
            return (
                new SimpleResponse(422, 'Coach already exists.')
            )($response);
        } catch (MemberNotFoundException) {
            return (new NotFoundResponse('Member not found'))($response);
        }

        $resource = new CoachResource(
            $coach,
            $request->getAttribute('kwai.user')
        );
        return (new JSONAPIResponse(
            JSONAPI\Document::createFromObject($resource)
        ))($response);
    }
}
