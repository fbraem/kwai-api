<?php
/**
 * @package Applications
 * @subpackage Facebook
 */
declare(strict_types=1);

namespace Kwai\Applications;

use Kwai\Core\Infrastructure\Configuration\Configuration;
use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Dependencies\DatabaseDependency;
use Kwai\Core\Infrastructure\Dependencies\FileSystemDependency;
use Kwai\Core\Infrastructure\Dependencies\Settings;
use Kwai\Core\Infrastructure\Dependencies\TemplateDependency;
use Kwai\Core\Infrastructure\Presentation\Action;
use Kwai\Core\Infrastructure\Presentation\Responses\NotFoundResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\SimpleResponse;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Core\Infrastructure\Template\PlatesEngine;
use Kwai\Modules\News\Domain\Exceptions\StoryNotFoundException;
use Kwai\Modules\News\Infrastructure\Repositories\StoryDatabaseRepository;
use Kwai\Modules\News\Infrastructure\Repositories\StoryImageRepository;
use Kwai\Modules\News\UseCases\GetStory;
use Kwai\Modules\News\UseCases\GetStoryCommand;
use League\Flysystem\Filesystem;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class FacebookStoryAction
 * @deprecated
 */
class FacebookStoryAction extends Action
{
    public function __construct(
        private ?Connection $database = null,
        private ?Filesystem $filesystem = null,
        private ?PlatesEngine $templateEngine = null,
        private ?Configuration $settings = null
    ) {
        parent::__construct();
        $this->database ??= depends('kwai.database', DatabaseDependency::class);
        $this->filesystem ??= depends('kwai.fs', FileSystemDependency::class);
        $this->templateEngine ??= depends('kwai.template_engine', TemplateDependency::class);
        $this->settings ??= depends('kwai.settings', Settings::class);
    }

    /**
     * @inheritDoc
     */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $command = new GetStoryCommand();
        $command->id = $args['id'];

        try {
            $story = (new GetStory(
                new StoryDatabaseRepository($this->database),
                new StoryImageRepository(
                    $this->filesystem,
                    $this->settings->getFilesystemConfiguration()->getUrl()
                )
            ))($command);
        } catch (StoryNotFoundException $exception) {
            return (new NotFoundResponse('Story not found'))($response);
        } catch (RepositoryException $exception) {
            return (new SimpleResponse(
                500,
                'A repository exception occurred'
            )
            )($response);
        }

        $facebook = false;
        $facebookUserAgent = 'facebookexternalhit/1.1';
        $userAgentStrLength = strlen($facebookUserAgent);

        $userAgents = $request->getHeader('User-Agent');
        foreach ($userAgents as $userAgent) {
            if (substr(strtolower($userAgent), 0, $userAgentStrLength) === $facebookUserAgent) {
                $facebook = true;
            }
        }

        $uri = $request->getUri();
        if ($uri->getPort() == 80) {
            $uri = $uri->withPort(null);
        }

        if ($facebook) {
            // This is the Facebook crawler ...
            $meta = [];
            $meta['og:url'] = $uri->__toString();
            $meta['og:type'] = 'article';
            $meta['og:title'] = $story->getContents()[0]->getTitle();
            $meta['og:description'] = $story->getContents()[0]->getSummary();

            $images = $story->getImages();
            $matchedImages = preg_grep('/header_detail_crop/', $images);
            if (count($matchedImages) > 0) {
                $meta['og:image'] = $uri->withPath(reset($matchedImages))->__toString();
                $meta['og:image:width'] = 800;
                $meta['og:image:height'] = 300;
            } else {
                $matchedImages = preg_grep('/header_overview_crop/', $images);
                if (count($matchedImages) > 0) {
                    $meta['og:image'] = $uri->withPath(reset($matchedImages))->__toString();
                    $meta['og:image:width'] = 500;
                    $meta['og:image:height'] = 500;
                }
            }

            $result = $this->templateEngine->createTemplate('facebook')->render(['meta' => $meta]);
            $response->getBody()->write($result);
        } else {
            return $response
                ->withHeader(
                    'Location',
                    $uri
                        ->withPath('/')
                        ->withQuery('')
                        ->__toString()
                    . '#/news/' . $story->id()
                )->withStatus(302);
        }
        return $response;
    }
}
