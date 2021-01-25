<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Presentation\REST;

use Kwai\Core\Infrastructure\Presentation\Responses\NotFoundResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\ResourceResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\SimpleResponse;
use Kwai\Core\Infrastructure\Presentation\Action;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\Exceptions\UserNotFoundException;
use Kwai\Modules\Users\Infrastructure\Repositories\UserDatabaseRepository;
use Kwai\Modules\Users\Presentation\Transformers\AbilityTransformer;
use Kwai\Modules\Users\UseCases\GetUserAbilities;
use Kwai\Modules\Users\UseCases\GetUserAbilitiesCommand;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class GetUserAbilitiesAction
 *
 * Action to get abilities from a given user.
 */
class GetUserAbilitiesAction extends Action
{
    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     */
    public function __invoke(Request $request, Response $response, $args)
    {
        $command = new GetUserAbilitiesCommand();
        $command->uuid = $args['uuid'];

        try {
            $database = $this->getContainerEntry('pdo_db');
            $abilities = GetUserAbilities::create(
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

        return (new ResourceResponse(
            AbilityTransformer::createForCollection(
                $abilities
            )
        ))($response);
    }
}
