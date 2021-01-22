<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Kwai\Applications\Author\Actions;

use Kwai\Core\Infrastructure\Presentation\Action;
use Kwai\Core\Infrastructure\Presentation\Responses\NotFoundResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\ResourceResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\SimpleResponse;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Pages\Domain\Exceptions\PageNotFoundException;
use Kwai\Modules\Pages\Infrastructure\Repositories\PageDatabaseRepository;
use Kwai\Modules\Pages\Infrastructure\Repositories\PageImageRepository;
use Kwai\Modules\Pages\Presentation\Transformers\PageTransformer;
use Kwai\Modules\Pages\UseCases\GetPage;
use Kwai\Modules\Pages\UseCases\GetPageCommand;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class GetPageAction
 */
class GetPageAction extends Action
{
    /**
     * @inheritDoc
     */
    public function __invoke(Request $request, Response $response, array $args)
    {
        $command = new GetPageCommand();
        $command->id = (int) $args['id'];

        $database = $this->getContainerEntry('pdo_db');
        $filesystem = $this->getContainerEntry('filesystem');

        try {
            $page = GetPage::create(
                new PageDatabaseRepository($database),
                new PageImageRepository(
                    $filesystem,
                    $this->getContainerEntry('settings')['files']['url']
                )
            )($command);
        } catch (RepositoryException $e) {
            $this->logException($e);
            return (new SimpleResponse(
                500,
                'A repository exception occurred'
            )
            )($response);
        } catch (PageNotFoundException) {
            return (new NotFoundResponse('Page not found'))($response);
        }

        $resource = PageTransformer::createForItem(
            $page,
            $this->getContainerEntry('converter')
        );

        return (new ResourceResponse(
            $resource
        ))($response);
    }
}
