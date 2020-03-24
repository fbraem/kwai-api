<?php
declare(strict_types = 1);

namespace Kwai\Modules\Users\Presentation\Rest;

use Core\Responses\NotFoundResponse;
use Kwai\Core\Domain\Exceptions\NotFoundException;
use Kwai\Core\Infrastructure\Presentation\Action;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Core\Responses\ResourceResponse;

use Kwai\Modules\Users\Presentation\Transformers\UserTransformer;
use Kwai\Modules\Users\Infrastructure\Repositories\UserDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\AbilityDatabaseRepository;
use Kwai\Modules\Users\UseCases\GetCurrentUserCommand;
use Kwai\Modules\Users\UseCases\GetCurrentUser;

/**
 * Class UserAction
 *
 * Action to get the logged in user.
 */
class UserAction extends Action
{
    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(Request $request, Response $response, $args)
    {
        $user = $request->getAttribute('kwai.user');
        $command = new GetCurrentUserCommand();
        $command->uuid = strval($user->getUuid());

        try {
            $database = $this->getContainerEntry('pdo_db');
            $user = (new GetCurrentUser(
                new UserDatabaseRepository($database),
                new AbilityDatabaseRepository($database)
            ))($command);
        } catch (NotFoundException $e) {
            return (new NotFoundResponse('User not found'))($response);
        }

        return (new ResourceResponse(
            UserTransformer::createForItem(
                $user
            )
        ))($response);
    }
}
