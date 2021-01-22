<?php
/**
 * @package Applications
 * @subpackage Admin
 */

declare(strict_types=1);

namespace Kwai\Applications\Admin\Actions;

use Kwai\Core\Infrastructure\Presentation\Responses\NotFoundResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\ResourceResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\SimpleResponse;
use Kwai\Core\Infrastructure\Presentation\Action;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\Exceptions\AbilityNotFoundException;
use Kwai\Modules\Users\Domain\Exceptions\UserNotFoundException;
use Kwai\Modules\Users\Infrastructure\Repositories\AbilityDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\UserDatabaseRepository;
use Kwai\Modules\Users\Presentation\Transformers\UserTransformer;
use Kwai\Modules\Users\UseCases\DetachAbilityFromUser;
use Kwai\Modules\Users\UseCases\DetachAbilityFromUserCommand;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class DetachAbilityAction extends Action
{
    /**
     * @inheritDoc
     */
    public function __invoke(Request $request, Response $response, array $args)
    {
        $command = new DetachAbilityFromUserCommand();
        $command->uuid = $args['uuid'];
        $command->abilityId = intval($args['ability']);

        $database = $this->getContainerEntry('pdo_db');
        $userRepo = new UserDatabaseRepository($database);
        $abilityRepo = new AbilityDatabaseRepository($database);

        try {
            $user = DetachAbilityFromUser::create(
                $userRepo,
                $abilityRepo
            )($command);
        } catch (RepositoryException $e) {
            $this->logException($e);
            return (
                new SimpleResponse(500, 'A repository exception occurred.')
            )($response);
        } catch (AbilityNotFoundException) {
            return (new NotFoundResponse('Ability not found'))($response);
        } catch (UserNotFoundException) {
            return (new NotFoundResponse('User not found'))($response);
        }

        return (new ResourceResponse(
            UserTransformer::createForItem(
                $user
            ),
            'abilities'
        ))($response);
    }
}
