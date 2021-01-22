<?php
/**
 * @package Applications
 * @subpackage Author
 */
declare(strict_types=1);

namespace Kwai\Applications\Author\Actions;

use Kwai\Core\Domain\ValueObjects\Creator;
use Kwai\Core\Infrastructure\Presentation\Action;
use Kwai\Core\Infrastructure\Presentation\InputSchemaProcessor;
use Kwai\Core\Infrastructure\Presentation\Responses\NotFoundResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\ResourceResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\SimpleResponse;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Pages\Domain\Exceptions\ApplicationNotFoundException;
use Kwai\Modules\Pages\Domain\Exceptions\PageNotFoundException;
use Kwai\Modules\Pages\Infrastructure\Repositories\ApplicationDatabaseRepository;
use Kwai\Modules\Pages\Infrastructure\Repositories\PageDatabaseRepository;
use Kwai\Modules\Pages\Infrastructure\Repositories\PageImageRepository;
use Kwai\Modules\Pages\Presentation\Transformers\PageTransformer;
use Kwai\Modules\Pages\UseCases\UpdatePage;
use Nette\Schema\ValidationException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class UpdatePageAction
 */
class UpdatePageAction extends Action
{
    /**
     * @inheritDoc
     */
    public function __invoke(Request $request, Response $response, array $args)
    {
        try {
            $command = InputSchemaProcessor::create(new PageInputSchema(false))
                ->process($request->getParsedBody());
        } catch (ValidationException $ve) {
            return (new SimpleResponse(422, $ve->getMessage()))($response);
        }

        $command->id = (int) $args['id'];

        $user = $request->getAttribute('kwai.user');
        $creator = new Creator($user->id(), $user->getUsername());

        $database = $this->getContainerEntry('pdo_db');
        $pageRepo = new PageDatabaseRepository($database);
        $applicationRepo = new ApplicationDatabaseRepository($database);

        $filesystem = $this->getContainerEntry('filesystem');
        $imageRepo = new PageImageRepository(
            $filesystem,
            $this->getContainerEntry('settings')['files']['url']
        );

        try {
            $page = UpdatePage::create(
                $pageRepo,
                $applicationRepo,
                $imageRepo
            )($command, $creator);
        } catch (RepositoryException $e) {
            $this->logException($e);
            return (new SimpleResponse(
                500,
                'A repository exception occurred'
            ))($response);
        } catch (ApplicationNotFoundException) {
            return (new NotFoundResponse('Application not found'))($response);
        } catch (PageNotFoundException) {
            return (new NotFoundResponse('Page not found'))($response);
        }

        return (new ResourceResponse(
            PageTransformer::createForItem(
                $page,
                $this->getContainerEntry('converter')
            )
        ))($response);
    }
}
