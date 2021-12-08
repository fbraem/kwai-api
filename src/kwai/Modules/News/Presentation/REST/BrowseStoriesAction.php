<?php
/**
 * @package Kwai
 * @subpackage News
 */
declare(strict_types=1);

namespace Kwai\Modules\News\Presentation\REST;

use Kwai\Core\Infrastructure\Converter\ConverterFactory;
use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Dependencies\ConvertDependency;
use Kwai\Core\Infrastructure\Dependencies\DatabaseDependency;
use Kwai\Core\Infrastructure\Dependencies\FileSystemDependency;
use Kwai\Core\Infrastructure\Dependencies\Settings;
use Kwai\Core\Infrastructure\Presentation\Action;
use Kwai\Core\Infrastructure\Presentation\Responses\JSONAPIResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\ResourceResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\SimpleResponse;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\News\Infrastructure\Repositories\StoryDatabaseRepository;
use Kwai\Modules\News\Infrastructure\Repositories\StoryImageRepository;
use Kwai\Modules\News\Presentation\Resources\StoryResource;
use Kwai\Modules\News\UseCases\BrowseStories;
use Kwai\Modules\News\UseCases\BrowseStoriesCommand;
use League\Flysystem\Filesystem;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Kwai\JSONAPI;

/**
 * Class BrowseStoriesAction
 *
 * Action to browse all news stories
 */
class BrowseStoriesAction extends Action
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

    public function __invoke(Request $request, Response $response, array $args)
    {
        $parameters = $request->getAttribute('parameters');

        $command = new BrowseStoriesCommand();
        $command->limit = (int) ($parameters['page']['limit'] ?? 10);
        $command->offset = (int) ($parameters['page']['offset'] ?? 0);
        if (isset($parameters['filter']['application'])) {
            $command->application = (int) $parameters['filter']['application'];
        }
        if (isset($parameters['filter']['year'])) {
            $command->publishYear = (int) $parameters['filter']['year'];
        }
        if (isset($parameters['filter']['month'])) {
            $command->publishMonth = (int) $parameters['filter']['month'];
        }
        $command->promoted = isset($parameters['filter']['promoted']);
        if (isset($parameters['filter']['application'])) {
            $command->application = (int) $parameters['filter']['application'];
        }
        if (isset($parameters['filter']['enabled'])) {
            // TODO: Not allowed for public view
            $command->enabled = $parameters['filter']['enabled'] === 'true';
        }

        try {
            [$count, $stories] = (new BrowseStories(
                new StoryDatabaseRepository($this->database),
                new StoryImageRepository(
                    $this->filesystem,
                    $this->settings['files']['url']
                )
            ))($command);
        } catch (QueryException $e) {
            $this->logException($e);
            return (
                new SimpleResponse(500, 'A query exception occurred.')
            )($response);
        } catch (RepositoryException $e) {
            $this->logException($e);
            return (
                new SimpleResponse(500, 'A repository exception occurred.')
            )($response);
        }

        $resources = $stories->map(
            fn ($story) => new StoryResource(
                $story,
                $this->converterFactory
            )
        );

        return (new JSONAPIResponse(
            JSONAPI\Document::createFromArray($resources->toArray())
            ->setMeta([
                'count' => $count,
                'limit' => $command->limit,
                'offset' => $command->offset
            ])
        ))($response);
    }
}
