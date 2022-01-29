<?php
/**
 * @package Modules
 * @subpackage Users
 */

declare(strict_types=1);

namespace Kwai\Modules\Users\Presentation\REST;

use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Dependencies\DatabaseDependency;
use Kwai\Core\Infrastructure\Presentation\Responses\JSONAPIResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\NotFoundResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\SimpleResponse;
use Kwai\Core\Infrastructure\Presentation\Action;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\JSONAPI;
use Kwai\Modules\Users\Domain\Exceptions\RoleNotFoundException;
use Kwai\Modules\Users\Domain\Exceptions\UserNotFoundException;
use Kwai\Modules\Users\Infrastructure\Repositories\RoleDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\UserDatabaseRepository;
use Kwai\Modules\Users\Presentation\Resources\UserResource;
use Kwai\Modules\Users\UseCases\DetachRoleFromUser;
use Kwai\Modules\Users\UseCases\DetachRoleFromUserCommand;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class DetachRoleAction extends Action
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
    public function __invoke(Request $request, Response $response, array $args)
    {
        $command = new DetachRoleFromUserCommand();
        $command->uuid = $args['uuid'];
        $command->roleId = intval($args['role']);

        $userRepo = new UserDatabaseRepository($this->database);
        $roleRepo = new RoleDatabaseRepository($this->database);

        try {
            $user = DetachRoleFromUser::create(
                $userRepo,
                $roleRepo
            )($command);
        } catch (RepositoryException $e) {
            $this->logException($e);
            return (
                new SimpleResponse(500, 'A repository exception occurred.')
            )($response);
        } catch (RoleNotFoundException) {
            return (new NotFoundResponse('Role not found'))($response);
        } catch (UserNotFoundException) {
            return (new NotFoundResponse('User not found'))($response);
        }

        $resource = new UserResource($user);
        return (new JSONAPIResponse(
            JSONAPI\Document::createFromObject($resource)
        ))($response);
    }
}
