<?php
declare(strict_types = 1);

namespace Kwai\Modules\Users\Presentation\Rest;

use Kwai\Core\Infrastructure\Presentation\Responses\NotFoundResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\ResourceResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\SimpleResponse;
use Kwai\Core\Domain\Exceptions\NotFoundException;
use Kwai\Core\Infrastructure\Presentation\Action;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Infrastructure\Repositories\AbilityDatabaseRepository;
use Kwai\Modules\Users\Presentation\Transformers\AbilityTransformer;
use Kwai\Modules\Users\Presentation\Transformers\UserTransformer;
use Kwai\Modules\Users\UseCases\GetAbility;
use Kwai\Modules\Users\UseCases\GetAbilityCommand;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class GetAbilityAction
 *
 * Action to get an ability with the given id.
 */
class GetAbilityAction extends Action
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
        $command = new GetAbilityCommand();
        $command->id = intval($args['id']);

        try {
            $database = $this->getContainerEntry('pdo_db');
            $ability = (new GetAbility(
                new AbilityDatabaseRepository($database)
            ))($command);
        } catch (NotFoundException $e) {
            return (new NotFoundResponse('Ability not found'))($response);
        } catch (RepositoryException $e) {
            return (
                new SimpleResponse(500, 'A repository exception occurred.')
            )($response);
        }

        return (new ResourceResponse(
            AbilityTransformer::createForItem(
                $ability
            )
        ))($response);
    }
}
