<?php
/**
 * @package Kwai
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\Presentation\Rest;

use Core\Responses\ResourceResponse;
use Core\Responses\SimpleResponse;
use Kwai\Core\Infrastructure\Presentation\Action;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Infrastructure\Repositories\AbilityDatabaseRepository;
use Kwai\Modules\Users\Presentation\Transformers\AbilityTransformer;
use Kwai\Modules\Users\UseCases\BrowseAbilities;
use Kwai\Modules\Users\UseCases\BrowseAbilitiesCommand;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class BrowseUserAction
 *
 * Action to browse all users
 */
class BrowseAbilitiesAction extends Action
{
    /**
     * @inheritDoc
     */
    public function __invoke(Request $request, Response $response, array $args)
    {
        $repo = new AbilityDatabaseRepository($this->getContainerEntry('pdo_db'));
        try {
            $users = (new BrowseAbilities($repo))(new BrowseAbilitiesCommand());
            return (new ResourceResponse(
                AbilityTransformer::createForCollection($users)
            ))($response);
        } catch (RepositoryException $e) {
            return (
                new SimpleResponse(500, 'An internal repository occurred.')
            )($response);
        }
    }
}
