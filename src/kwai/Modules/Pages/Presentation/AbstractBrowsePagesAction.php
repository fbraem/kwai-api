<?php
/**
 * @package Pages
 * @subpackage Presentation
 */
declare(strict_types=1);

namespace Kwai\Modules\Pages\Presentation;

use Kwai\Core\Infrastructure\Converter\ConverterFactory;
use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Dependencies\ConvertDependency;
use Kwai\Core\Infrastructure\Dependencies\DatabaseDependency;
use Kwai\Core\Infrastructure\Dependencies\FileSystemDependency;
use Kwai\Core\Infrastructure\Presentation\Action;
use Kwai\Core\Infrastructure\Presentation\Responses\ResourceResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\SimpleResponse;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Pages\Infrastructure\Repositories\PageDatabaseRepository;
use Kwai\Modules\Pages\Infrastructure\Repositories\PageImageRepository;
use Kwai\Modules\Pages\Presentation\Transformers\PageTransformer;
use Kwai\Modules\Pages\UseCases\BrowsePages;
use Kwai\Modules\Pages\UseCases\BrowsePagesCommand;
use League\Flysystem\Filesystem;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class BrowsePagesAction
 *
 * Base action class to browse all news stories
 */
abstract class AbstractBrowsePagesAction extends Action
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
     * Create the default command for the use case Browse Pages.
     *
     * @param Request $request
     * @param array   $args
     * @return BrowsePagesCommand
     */
    protected function createCommand(Request $request, array $args): BrowsePagesCommand
    {
        $command = new BrowsePagesCommand();
        $parameters = $request->getAttribute('parameters');
        $command->limit = (int) ($parameters['page']['limit'] ?? 10);
        $command->offset = (int) ($parameters['page']['offset'] ?? 0);
        if (isset($parameters['filter']['application'])) {
            $command->application = (int) $parameters['filter']['application'];
        }
        $sort = $parameters['sort'] ?? [];
        if (in_array('application', $sort)) {
            $command->sort = BrowsePagesCommand::SORT_APPLICATION;
        } elseif (in_array('priority', $sort)) {
            $command->sort = BrowsePagesCommand::SORT_PRIORITY;
        } elseif (in_array('date', $sort)) {
            $command->sort = BrowsePagesCommand::SORT_CREATION_DATE;
        } else {
            $command->sort = BrowsePagesCommand::SORT_PRIORITY;
        }
        return $command;
    }

    /**
     * @inheritDoc
     */
    public function __invoke(Request $request, Response $response, array $args)
    {
        $command = $this->createCommand($request, $args);

        try {
            [$count, $pages] = BrowsePages::create(
                new PageDatabaseRepository($this->database),
                new PageImageRepository(
                    $this->filesystem,
                    $this->settings['files']['url']
                )
            )($command);
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

        $resource = PageTransformer::createForCollection(
            $pages,
            $this->converterFactory
        );
        $resource->setMeta([
            'limit' => $command->limit,
            'offset' => $command->offset,
            'count' => $count
        ]);

        return (new ResourceResponse(
            $resource
        ))($response);
    }
}
