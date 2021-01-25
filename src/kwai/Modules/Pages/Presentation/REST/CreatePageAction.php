<?php
/**
 * @package Modules
 * @subpackage Pages
 */
declare(strict_types=1);

namespace Kwai\Modules\Pages\Presentation\REST;

use Kwai\Core\Domain\ValueObjects\Creator;
use Kwai\Core\Infrastructure\Presentation\Action;
use Kwai\Core\Infrastructure\Presentation\InputSchemaProcessor;
use Kwai\Core\Infrastructure\Presentation\Responses\NotFoundResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\ResourceResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\SimpleResponse;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Pages\Domain\Exceptions\ApplicationNotFoundException;
use Kwai\Modules\Pages\Infrastructure\Repositories\ApplicationDatabaseRepository;
use Kwai\Modules\Pages\Infrastructure\Repositories\PageDatabaseRepository;
use Kwai\Modules\Pages\Infrastructure\Repositories\PageImageRepository;
use Kwai\Modules\Pages\Presentation\Transformers\PageTransformer;
use Kwai\Modules\Pages\UseCases\CreatePage;
use Nette\Schema\ValidationException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class CreatePageAction
 */
class CreatePageAction extends Action
{
    /**
     * @inheritDoc
     */
    public function __invoke(Request $request, Response $response, array $args)
    {
        try {
            $command = InputSchemaProcessor::create(new PageInputSchema(true))
                ->process($request->getParsedBody());
        } catch (ValidationException $ve) {
            return (new SimpleResponse(422, $ve->getMessage()))($response);
        }

        $user = $request->getAttribute('kwai.user');
        $creator = new Creator($user->id, $user->getUsername());

        $database = $this->getContainerEntry('pdo_db');
        $pageRepo = new PageDatabaseRepository($database);
        $applicationRepo = new ApplicationDatabaseRepository($database);

        $filesystem = $this->getContainerEntry('filesystem');
        $imageRepo = new PageImageRepository(
            $filesystem,
            $this->getContainerEntry('settings')['files']['url']
        );

        try {
            $page = CreatePage::create(
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
        }

        return (new ResourceResponse(
            PageTransformer::createForItem(
                $page,
                $this->getContainerEntry('converter')
            )
        ))($response);
    }
}
