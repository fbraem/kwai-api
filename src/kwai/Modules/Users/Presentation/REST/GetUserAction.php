<?php
/**
 * @package Modules
 * @subpackage User
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Presentation\REST;

use Kwai\Core\Infrastructure\Presentation\Responses\NotAuthorizedResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\NotFoundResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\SimpleResponse;
use Kwai\Core\Infrastructure\Presentation\Action;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\Exceptions\UserNotFoundException;
use Kwai\Modules\Users\UseCases\GetUser;
use Kwai\Modules\Users\UseCases\GetUserCommand;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Kwai\Core\Infrastructure\Presentation\Responses\ResourceResponse;
use Kwai\Modules\Users\Presentation\Transformers\UserTransformer;
use Kwai\Modules\Users\Infrastructure\Repositories\UserDatabaseRepository;

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
        if (isset($args['uuid'])) {
            $command = new GetUserCommand();
            $command->uuid = $args['uuid'];

            try {
                $database = $this->getContainerEntry('pdo_db');
                $user = GetUser::create(
                    new UserDatabaseRepository($database)
                )($command);
            } catch (RepositoryException $e) {
                $this->logException($e);
                return (
                new SimpleResponse(500, 'A repository exception occurred.')
                )($response);
            } catch (UserNotFoundException) {
                return (new NotFoundResponse('User not found'))($response);
            }
        } else {
            $user = $request->getAttribute('kwai.user');
        }

        if ($user) {
            return (new ResourceResponse(
                UserTransformer::createForItem(
                    $user
                ),
                'abilities'
            ))($response);
        }

        return (new NotAuthorizedResponse())($response);
    }
}
