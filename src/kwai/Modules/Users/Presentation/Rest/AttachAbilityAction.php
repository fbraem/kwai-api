<?php
/**
 * @package Kwai
 * @subpackage Users
 */

declare(strict_types=1);

namespace Kwai\Modules\Users\Presentation\Rest;

use Core\Responses\NotFoundResponse;
use Core\Responses\ResourceResponse;
use Core\Responses\SimpleResponse;
use Kwai\Core\Domain\Exceptions\NotFoundException;
use Kwai\Core\Infrastructure\Presentation\Action;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Infrastructure\Repositories\AbilityDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\UserDatabaseRepository;
use Kwai\Modules\Users\Presentation\Transformers\UserTransformer;
use Kwai\Modules\Users\UseCases\AttachAbilityToUser;
use Kwai\Modules\Users\UseCases\AttachAbilityToUserCommand;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class AttachAbilityAction extends Action
{
    /**
     * @inheritDoc
     */
    public function __invoke(Request $request, Response $response, array $args)
    {
        $command = new AttachAbilityToUserCommand();
        $command->uuid = $args['uuid'];
        $command->abilityId = intval($args['ability']);

        $database = $this->getContainerEntry('pdo_db');
        $userRepo = new UserDatabaseRepository($database);
        $abilityRepo = new AbilityDatabaseRepository($database);

        try {
            $user = (new AttachAbilityToUser($userRepo, $abilityRepo))($command);
        } catch (NotFoundException $e) {
            return (new NotFoundResponse('User or ability not found'))($response);
        } catch (RepositoryException $e) {
            return (
                new SimpleResponse(500, 'A repository exception occurred.')
            )($response);
        }

        return (new ResourceResponse(
            UserTransformer::createForItem(
                $user
            ),
            'abilities'
        ))($response);
    }
}
