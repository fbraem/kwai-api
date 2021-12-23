<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Presentation\REST;

use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Dependencies\DatabaseDependency;
use Kwai\Core\Infrastructure\Presentation\Responses\JSONAPIResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\NotFoundResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\SimpleResponse;
use Kwai\Core\Infrastructure\Presentation\Action;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\JSONAPI;
use Kwai\Modules\Users\Domain\Exceptions\UserNotFoundException;
use Kwai\Modules\Users\Infrastructure\Repositories\UserDatabaseRepository;
use Kwai\Modules\Users\Presentation\Resources\AbilityResource;
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
    public function __construct(
        private ?Connection $database = null
    ) {
        parent::__construct();
        $this->database ??= depends('kwai.database', DatabaseDependency::class);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function __invoke(Request $request, Response $response, array $args)
    {
        $command = new GetUserAbilitiesCommand();
        $command->uuid = $args['uuid'];

        try {
            $abilities = GetUserAbilities::create(
                new UserDatabaseRepository($this->database)
            )($command);
        } catch (RepositoryException $e) {
            $this->logException($e);
            return (
                new SimpleResponse(500, 'A repository exception occurred.')
            )($response);
        } catch (UserNotFoundException) {
            return (new NotFoundResponse('User not found'))($response);
        }

        $resources = $abilities->map(fn ($ability) => new AbilityResource($ability));
        return (new JSONAPIResponse(
            JSONAPI\Document::createFromArray($resources->toArray())
                ->setMeta('count', $abilities->count())
        ))($response);
    }
}
