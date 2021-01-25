<?php
/**
 * @package Modules
 * @subpackage News
 */
declare(strict_types=1);

namespace Kwai\Modules\News\Presentation\REST;

use Kwai\Core\Infrastructure\Presentation\Action;
use Kwai\Core\Infrastructure\Presentation\Responses\JSONResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\SimpleResponse;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\News\Infrastructure\Repositories\StoryDatabaseRepository;
use Kwai\Modules\News\UseCases\GetArchive;
use Kwai\Modules\News\UseCases\GetArchiveCommand;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class GetArchiveAction
 */
class GetArchiveAction extends Action
{
    /**
     * @inheritDoc
     */
    public function __invoke(Request $request, Response $response, array $args)
    {
        $command = new GetArchiveCommand();
        $db = $this->getContainerEntry('pdo_db');
        try {
            $archive = GetArchive::create(
                new StoryDatabaseRepository($db)
            )($command);
        } catch (RepositoryException $e) {
            $this->logException($e);
            return (
                new SimpleResponse(500, 'A query exception occurred.')
            )($response);
        }

        return (new JSONResponse($archive))($response);
    }
}
