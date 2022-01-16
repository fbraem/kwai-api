<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\Presentation\REST;

use Kwai\Core\Domain\Exceptions\UnprocessableException;
use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Dependencies\DatabaseDependency;
use Kwai\Core\Infrastructure\Presentation\Action;
use Kwai\Core\Infrastructure\Presentation\InputSchemaProcessor;
use Kwai\Core\Infrastructure\Presentation\Responses\JSONAPIResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\NotFoundResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\SimpleResponse;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\JSONAPI;
use Kwai\Modules\Users\Domain\Exceptions\UserNotFoundException;
use Kwai\Modules\Users\Infrastructure\Repositories\AbilityDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\UserDatabaseRepository;
use Kwai\Modules\Users\Presentation\Resources\UserResource;
use Kwai\Modules\Users\UseCases\UpdateUser;
use Nette\Schema\ValidationException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;

/**
 * Class UpdateUserAction
 */
class UpdateUserAction extends Action
{
    public function __construct(
        private ?Connection $database = null,
        LoggerInterface $logger = null
    ) {
        parent::__construct($logger);
        $this->database ??= depends('kwai.database', DatabaseDependency::class);
    }

    /**
     * @inheritDoc
     */
    public function __invoke(Request $request, Response $response, array $args)
    {
        try {
            $command = InputSchemaProcessor::create(new UserSchema())
                ->process($request->getParsedBody());
        } catch (ValidationException $ve) {
            return (new SimpleResponse(422, $ve->getMessage()))($response);
        }

        if ($command->uuid != $args['uuid']) {
            return (new SimpleResponse(400, 'id in body and url should be the same.'))($response);
        }

        $user = $request->getAttribute('kwai.user');

        try {
            $user = UpdateUser::create(
                new UserDatabaseRepository($this->database),
                new AbilityDatabaseRepository($this->database)
            )($command, $user);
        } catch (RepositoryException) {
            return (
                new SimpleResponse(500, 'A repository exception occurred.')
            )($response);
        } catch (UserNotFoundException $e) {
            return (new NotFoundResponse((string) $e))($response);
        } catch (UnprocessableException $e) {
            return (
                new SimpleResponse(500, (string) $e)
            )($response);
        } catch (QueryException) {
            return (
                new SimpleResponse(500, 'A query exception occurred.')
            )($response);
        }

        $resource = new UserResource($user);
        return (new JSONAPIResponse(
            JSONAPI\Document::createFromObject($resource)
        ))($response);
    }
}
