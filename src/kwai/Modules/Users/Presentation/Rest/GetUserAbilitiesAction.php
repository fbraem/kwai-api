<?php
declare(strict_types = 1);

namespace Kwai\Modules\Users\Presentation\Rest;

use Core\Responses\NotFoundResponse;
use Core\Responses\ResourceResponse;
use Core\Responses\SimpleResponse;
use Kwai\Core\Domain\Exceptions\NotFoundException;
use Kwai\Core\Infrastructure\Presentation\Action;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Infrastructure\Repositories\AbilityDatabaseRepository;
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
            $abilities = (new GetUserAbilities(
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
            AbilityTransformer::createForCollection(
                $abilities
            )
        ))($response);
    }
}
