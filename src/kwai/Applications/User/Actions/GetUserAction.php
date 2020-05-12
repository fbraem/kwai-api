<?php
/**
 * @package Applications
 * @subpackage User
 */
declare(strict_types = 1);

namespace Kwai\Applications\User\Actions;

use Kwai\Core\Infrastructure\Presentation\Responses\NotFoundResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\SimpleResponse;
use Kwai\Core\Domain\Exceptions\NotFoundException;
use Kwai\Core\Infrastructure\Presentation\Action;

use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\UseCases\GetUser;
use Kwai\Modules\Users\UseCases\GetUserCommand;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Kwai\Core\Infrastructure\Presentation\Responses\ResourceResponse;

use Kwai\Modules\Users\Presentation\Transformers\UserTransformer;
use Kwai\Modules\Users\Infrastructure\Repositories\UserDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\AbilityDatabaseRepository;

/**
 * Class UserAction
 *
 * Action to get the logged in user.
 */
class GetUserAction extends Action
{
    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     */
    public function __invoke(Request $request, Response $response, $args)
    {
        $user = $request->getAttribute('kwai.user');
        $command = new GetUserCommand();
        $command->uuid = strval($user->getUuid());
        $command->withAbilities = true;

        try {
            $database = $this->getContainerEntry('pdo_db');
            $user = (new GetUser(
                new UserDatabaseRepository($database),
                new AbilityDatabaseRepository($database)
            ))($command);
        } catch (NotFoundException $e) {
            return (new NotFoundResponse('User not found'))($response);
        } catch (RepositoryException $e) {
            return (
                new SimpleResponse(500, 'A repository exception occurred.')
            )($response);
        }

        return (new ResourceResponse(
            UserTransformer::createForItem(
                $user
            )
        ))($response);
    }
}
