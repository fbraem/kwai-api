<?php
/**
 * @package Kwai
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\Presentation\Rest;

use Core\Responses\ResourceResponse;
use Core\Responses\SimpleResponse;
use Kwai\Core\Infrastructure\Database\DatabaseException;
use Kwai\Core\Infrastructure\Presentation\Action;
use Kwai\Modules\Users\Infrastructure\Repositories\UserDatabaseRepository;
use Kwai\Modules\Users\Presentation\Transformers\UserTransformer;
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
            return (new ResourceResponse(
                UserTransformer::createForCollection($repo->getAll())
            ))($response);
        } catch (DatabaseException $e) {
            return (
                new SimpleResponse(500, 'A database error occurred')
            )($response);
        }
    }
}
