<?php

namespace REST\Pages\Actions;

use Psr\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Domain\Page\PageTransformer;
use Domain\Page\PagesTable;

use Kwai\Core\Infrastructure\Presentation\Responses\UnprocessableEntityResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\ResourceResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\NotFoundResponse;

use Respect\Validation\Validator as v;

use Kwai\Core\Infrastructure\Validators\ValidationException;
use Kwai\Core\Infrastructure\Validators\InputValidator;
use Kwai\Core\Infrastructure\Validators\EntityExistValidator;

use Cake\Datasource\Exception\RecordNotFoundException;

class UpdateAction
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $pagesTable = PagesTable::getTableFromRegistry();
        try {
            $page = $pagesTable->get($args['id'], [
                'contain' => [
                    'Contents',
                    'Category',
                    'Contents.User'
                ]
            ]);

            $data = $request->getParsedBody();

            (new InputValidator([
                'data.attributes.priority' => v::digit(),
                'data.attributes.enabled' => v::boolType()
            ], true))->validate($data);

            $category = (new EntityExistValidator(
                'data.relationships.category',
                $pagesTable->Category,
                false
            ))->validate($data);

            $attributes = \JmesPath\search('data.attributes', $data);

            if (isset($category)) {
                $page->catgory = $category;
            }
            if (isset($attributes['priority'])) {
                $page->priority = $attributes['priority'];
            }
            if (isset($attributes['enabled'])) {
                $page->enabled = $attributes['enabled'];
            }
            if (isset($attributes['remark'])) {
                $page->remark = $attributes['remark'];
            }

            if (isset($attributes['contents'][0]['title'])) {
                $page->contents[0]->title = $attributes['contents'][0]['title'];
                $page->setDirty('contents', true);
            }
            if (isset($attributes['contents'][0]['summary'])) {
                $page->contents[0]->summary = $attributes['contents'][0]['summary'];
                $page->setDirty('contents', true);
            }
            if (isset($attributes['contents'][0]['content'])) {
                $page->contents[0]->content = $attributes['contents'][0]['content'];
                $page->setDirty('contents', true);
            }
            if (isset($attributes['contents'][0]['format'])) {
                $page->contents[0]->format = $attributes['contents'][0]['format'];
                $page->setDirty('contents', true);
            }
            if (isset($attributes['contents'][0]['locale'])) {
                $page->contents[0]->locale = $attributes['contents'][0]['locale'];
                $page->setDirty('contents', true);
            }
            if ($page->isDirty('contents')) {
                $page->contents[0]->user = $request->getAttribute('clubman.user');
            }
            $pagesTable->save($page, [
                'associated' => [
                    'Contents'
                ]
            ]);

            $filesystem = $this->container->get('filesystem');

            $response = (new ResourceResponse(
                PageTransformer::createForItem($page, $filesystem)
            ))($response);
        } catch (RecordNotFoundException $rnfe) {
            $response = (new NotFoundResponse(_("Page doesn't exist")))($response);
        } catch (ValidationException $ve) {
            $response = (new UnprocessableEntityResponse($ve->getErrors()));
        }

        return $response;
    }
}
