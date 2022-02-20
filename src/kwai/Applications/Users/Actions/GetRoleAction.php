<?php
/**
 * @package Applications
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Applications\Users\Actions;

use Kwai\Applications\Users\Resources\RoleResource;
use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Dependencies\DatabaseDependency;
use Kwai\Core\Infrastructure\Presentation\Action;
use Kwai\Core\Infrastructure\Presentation\Responses\JSONAPIResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\NotFoundResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\SimpleResponse;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\JSONAPI;
use Kwai\Modules\Users\Domain\Exceptions\RoleNotFoundException;
use Kwai\Modules\Users\Infrastructure\Repositories\RoleDatabaseRepository;
use Kwai\Modules\Users\UseCases\GetRole;
use Kwai\Modules\Users\UseCases\GetRoleCommand;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Symfony\Component\Routing\Annotation\Route;
use function depends;

/**
 * Class GetRoleAction
 *
 * Action to get a role with the given id.
 */
#[Route(
    path: '/users/roles/{id}',
    name: 'users.roles.get',
    requirements: [ 'id' => '\d+'],
    options: ['auth' => true],
    methods: ['GET']
)]
class GetRoleAction extends Action
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
        $command = new GetRoleCommand();
        $command->id = intval($args['id']);

        try {
            $role = GetRole::create(
                new RoleDatabaseRepository($this->database)
            )($command);
        } catch (RepositoryException $e) {
            $this->logException($e);
            return (
                new SimpleResponse(500, 'A repository exception occurred.')
            )($response);
        } catch (RoleNotFoundException) {
            return (new NotFoundResponse('Role not found'))($response);
        }

        $resource = new RoleResource($role);

        return (new JSONAPIResponse(
            JSONAPI\Document::createFromObject($resource)
        ))($response);
    }
}
