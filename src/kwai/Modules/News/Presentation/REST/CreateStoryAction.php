<?php
/**
 * @package Modules
 * @subpackage News
 */
declare(strict_types=1);

namespace Kwai\Modules\News\Presentation\REST;

use Kwai\Core\Domain\ValueObjects\Creator;
use Kwai\Core\Infrastructure\Configuration\Configuration;
use Kwai\Core\Infrastructure\Converter\ConverterFactory;
use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Dependencies\ConvertDependency;
use Kwai\Core\Infrastructure\Dependencies\DatabaseDependency;
use Kwai\Core\Infrastructure\Dependencies\FileSystemDependency;
use Kwai\Core\Infrastructure\Dependencies\Settings;
use Kwai\Core\Infrastructure\Presentation\Action;
use Kwai\Core\Infrastructure\Presentation\InputSchemaProcessor;
use Kwai\Core\Infrastructure\Presentation\Responses\JSONAPIResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\NotFoundResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\SimpleResponse;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\News\Domain\Exceptions\ApplicationNotFoundException;
use Kwai\Modules\News\Infrastructure\Repositories\ApplicationDatabaseRepository;
use Kwai\Modules\News\Infrastructure\Repositories\StoryDatabaseRepository;
use Kwai\Modules\News\Infrastructure\Repositories\StoryImageRepository;
use Kwai\Modules\News\Presentation\Resources\StoryResource;
use Kwai\Modules\News\UseCases\CreateStory;
use League\Flysystem\Filesystem;
use Nette\Schema\ValidationException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Kwai\JSONAPI;

/**
 * Class CreateStoryAction
 */
class CreateStoryAction extends Action
{
    public function __construct(
        private ?Connection $database = null,
        private ?Filesystem $filesystem = null,
        private ?ConverterFactory $converterFactory = null,
        private ?Configuration $settings = null
    ) {
        parent::__construct();
        $this->database ??= depends('kwai.database', DatabaseDependency::class);
        $this->filesystem ??= depends('kwai.fs', FileSystemDependency::class);
        $this->converterFactory ??= depends('kwai.converter', ConvertDependency::class);
        $this->settings ??= depends('kwai.settings', Settings::class);
    }

    /**
     * @inheritDoc
     */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        try {
            $command = InputSchemaProcessor::create(new StorySchema(true))
                ->process($request->getParsedBody());
        } catch (ValidationException $ve) {
            return (new SimpleResponse(422, $ve->getMessage()))($response);
        }

        $user = $request->getAttribute('kwai.user');
        $creator = new Creator($user->id(), $user->getUsername());

        $storyRepo = new StoryDatabaseRepository($this->database);
        $categoryRepo = new ApplicationDatabaseRepository($this->database);

        $imageRepo = new StoryImageRepository(
            $this->filesystem,
            $this->settings->getFilesystemConfiguration()->getUrl()
        );

        try {
            $story = CreateStory::create(
                $storyRepo,
                $categoryRepo,
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

        return (new JSONAPIResponse(
            JSONAPI\Document::createFromObject(
                new StoryResource($story, $this->converterFactory)
            )
        ))($response);
    }
}
