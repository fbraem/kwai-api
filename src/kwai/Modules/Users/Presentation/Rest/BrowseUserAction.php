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
use Kwai\Modules\Users\Infrastructure\Repositories\UserDatabaseRepository;
use Kwai\Modules\Users\Presentation\Transformers\UserTransformer;
use Kwai\Modules\Users\UseCases\BrowseUser;
use Kwai\Modules\Users\UseCases\BrowseUserCommand;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class BrowseUserAction
 *
 * Action to browse all users
 */
class BrowseUserAction extends Action
{
    /**
     * @inheritDoc
     */
    public function __invoke(Request $request, Response $response, array $args)
    {
        $repo = new UserDatabaseRepository($this->getContainerEntry('pdo_db'));
        try {
            $users = (new BrowseUser($repo))(new BrowseUserCommand());
            return (new ResourceResponse(
                UserTransformer::createForCollection($users)
            ))($response);
        } catch (RepositoryException $e) {
            return (
                new SimpleResponse(500, 'An internal repository occurred.')
            )($response);
        }
    }
}
