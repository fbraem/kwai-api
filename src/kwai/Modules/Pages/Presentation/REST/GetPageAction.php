<?php
/**
 * @package Modules
 * @subpackage Pages
 */
declare(strict_types=1);

namespace Kwai\Modules\Pages\Presentation\REST;

use Kwai\Core\Infrastructure\Configuration\Configuration;
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
use Kwai\JSONAPI;
use Kwai\Modules\Pages\Domain\Exceptions\PageNotFoundException;
use Kwai\Modules\Pages\Infrastructure\Repositories\PageDatabaseRepository;
use Kwai\Modules\Pages\Infrastructure\Repositories\PageImageRepository;
use Kwai\Modules\Pages\Presentation\Resources\PageResource;
use Kwai\Modules\Pages\UseCases\GetPage;
use Kwai\Modules\Pages\UseCases\GetPageCommand;
use League\Flysystem\Filesystem;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class GetPageAction
 */
class GetPageAction extends Action
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
        $command = new GetPageCommand();
        $command->id = (int) $args['id'];

        try {
            $page = GetPage::create(
                new PageDatabaseRepository($this->database),
                new PageImageRepository(
                    $this->filesystem,
                    $this->settings->getFilesystemConfiguration()->getUrl()
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

        $resource = new PageResource(
            $page,
            $this->converterFactory
        );

        return (new JSONAPIResponse(
            JSONAPI\Document::createFromObject($resource)
        ))($response);
    }
}
