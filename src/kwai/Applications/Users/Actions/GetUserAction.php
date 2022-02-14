<?php
/**
 * @package Applications
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Applications\Users\Actions;

use Kwai\Applications\Application;
use Kwai\Applications\Users\Resources\UserResource;
use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Dependencies\DatabaseDependency;
use Kwai\Core\Infrastructure\Presentation\Action;
use Kwai\Core\Infrastructure\Presentation\Responses\JSONAPIResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\NotFoundResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\SimpleResponse;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\JSONAPI;
use Kwai\Modules\Users\Domain\Exceptions\UserNotFoundException;
use Kwai\Modules\Users\Infrastructure\Repositories\UserDatabaseRepository;
use Kwai\Modules\Users\UseCases\GetUser;
use Kwai\Modules\Users\UseCases\GetUserCommand;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Symfony\Component\Routing\Annotation\Route;
use function depends;

/**
 * Class UserAction
 *
 * Action to get a user.
 */
#[Route(
    path: '/users/{uuid}',
    name: 'users.get',
    requirements: ['uuid' => Application::UUID_REGEX],
    defaults: ['auth' => true],
    methods: ['GET']
)]
class GetUserAction extends Action
{
    public function __construct(
        private ?Connection $database = null
    ) {
        parent::__construct();
        $this->database ??= depends('kwai.database', DatabaseDependency::class);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $command = new GetUserCommand();
        $command->uuid = $args['uuid'];

        try {
            $user = GetUser::create(
                new UserDatabaseRepository($this->database)
            )($command);
        } catch (RepositoryException $e) {
            $this->logException($e);
            return (
                new SimpleResponse(500, 'A repository exception occurred.')
            )($response);
        } catch (UserNotFoundException) {
            return (new NotFoundResponse('User not found'))($response);
        }

        $resource = new UserResource(
            $user,
            $request->getAttribute('kwai.user')
        );
        return (new JSONAPIResponse(
            JSONAPI\Document::createFromObject($resource)
        ))($response);
    }
}
