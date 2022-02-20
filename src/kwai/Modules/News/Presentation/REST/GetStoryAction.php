<?php
/**
 * @package Modules
 * @subpackage News
 */
declare(strict_types=1);

namespace Kwai\Modules\News\Presentation\REST;

use Kwai\Core\Infrastructure\Converter\ConverterFactory;
use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Dependencies\ConvertDependency;
use Kwai\Core\Infrastructure\Dependencies\DatabaseDependency;
use Kwai\Core\Infrastructure\Dependencies\FileSystemDependency;
use Kwai\Core\Infrastructure\Dependencies\Settings;
use Kwai\Core\Infrastructure\Presentation\Action;
use Kwai\Core\Infrastructure\Presentation\Responses\JSONAPIResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\NotFoundResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\SimpleResponse;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\News\Domain\Exceptions\StoryNotFoundException;
use Kwai\Modules\News\Infrastructure\Repositories\StoryDatabaseRepository;
use Kwai\Modules\News\Infrastructure\Repositories\StoryImageRepository;
use Kwai\Modules\News\Presentation\Resources\StoryResource;
use Kwai\Modules\News\UseCases\GetStory;
use Kwai\Modules\News\UseCases\GetStoryCommand;
use League\Flysystem\Filesystem;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Kwai\JSONAPI;

/**
 * Class GetStoryAction
 */
class GetStoryAction extends Action
{
    public function __construct(
        private ?Connection $database = null,
        private ?Filesystem $filesystem = null,
        private ?ConverterFactory $converterFactory = null,
        private ?array $settings = null
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
        $command = new GetStoryCommand();
        $command->id = (int) $args['id'];

        try {
            $story = GetStory::create(
                new StoryDatabaseRepository($this->database),
                new StoryImageRepository(
                    $this->filesystem,
                    $this->settings['files']['url']
                )
            )($command);
        } catch (RepositoryException $e) {
            $this->logException($e);
            return (new SimpleResponse(
                500,
                'A repository exception occurred'
            )
            )($response);
        } catch (StoryNotFoundException) {
            return (new NotFoundResponse('Story not found'))($response);
        }

        return (new JSONAPIResponse(
            JSONAPI\Document::createFromObject(
                new StoryResource($story, $this->converterFactory)
            )
        ))($response);
    }
}
