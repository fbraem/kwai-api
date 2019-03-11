<?php

namespace REST\News\Actions;

use Interop\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Domain\News\NewsStoryTransformer;
use Domain\News\NewsStoriesTable;
use REST\News\NewsStoryValidator;
use Domain\Category\CategoriesTable;

use Cake\Datasource\Exception\RecordNotFoundException;

use Respect\Validation\Validator as v;

use Core\Validators\ValidationException;
use Core\Validators\InputValidator;
use Core\Validators\EntityExistValidator;

use Core\Responses\NotFoundResponse;
use Core\Responses\ResourceResponse;
use Core\Responses\UnprocessableEntityResponse;

class UpdateStoryAction
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $storiesTable = NewsStoriesTable::getTableFromRegistry();
        try {
            $story = $storiesTable->get($args['id'], [
                'contain' => [
                    'Category',
                    'Contents',
                    'Contents.User'
                ]
            ]);
        } catch (RecordNotFoundException $rnfe) {
            return (new NotFoundResponse(_("Story doesn't exist")))($response);
        }

        $data = $request->getParsedBody();

        try {
            (new InputValidator([
                'data.attributes.featured' => v::digit(),
                'data.attributes.featured_end_date' => v::date('Y-m-d H:i:s'),
                'data.attributes.publish_date' => v::date('Y-m-d H:i:s'),
                'data.attributes.timezone' => v::notEmpty()->length(1, 255),
                'data.attributes.end_date' => v::date('Y-m-d H:i:s'),
                'data.attributes.enabled' => v::boolType()
            ], true))->validate($data);

            $category = (new EntityExistValidator('data.relationships.category', $storiesTable->Category, false))->validate($data);

            $attributes = \JmesPath\search('data.attributes', $data);

            if (isset($attributes['contents'][0]['title'])) {
                $story->contents[0]->title = $attributes['contents'][0]['title'];
                $story->dirty('contents', true);
            }
            if (isset($attributes['contents'][0]['summary'])) {
                $story->contents[0]->summary = $attributes['contents'][0]['summary'];
                $story->dirty('contents', true);
            }
            if (isset($attributes['contents'][0]['content'])) {
                $story->contents[0]->content = $attributes['contents'][0]['content'];
                $story->dirty('contents', true);
            }
            if (isset($attributes['contents'][0]['format'])) {
                $story->contents[0]->format = $attributes['contents'][0]['format'];
                $story->dirty('contents', true);
            }
            if (isset($attributes['contents'][0]['locale'])) {
                $story->contents[0]->locale = $attributes['contents'][0]['locale'];
                $story->dirty('contents', true);
            }
            if ($story->isDirty('contents')) {
                $story->contents[0]->user = $request->getAttribute('clubman.user');
            }

            if (isset($category)) {
                $story->category = $category;
            }
            if (isset($attributes['publish_date'])) {
                $story->publish_date = $attributes['publish_date'];
            }
            if (isset($attributes['end_date'])) {
                $story->end_date = $attributes['end_date'];
            }
            if (isset($attributes['featured'])) {
                $story->featured = $attributes['featured'];
            }
            if (isset($attributes['featured_end_date'])) {
                $story->featured_end_date = $attributes['featured_end_date'];
            }
            if (isset($attributes['enabled'])) {
                $story->enabled = $attributes['enabled'];
            }
            if (isset($attributes['timezone'])) {
                $story->timezone = $attributes['timezone'];
            }
            if (isset($attributes['remark'])) {
                $story->remark = $attributes['remark'];
            }

            $storiesTable->save($story, [
                'associated' => [
                    'Contents'
                ]
            ]);

            $filesystem = $this->container->get('filesystem');

            $response = (new ResourceResponse(
                NewsStoryTransformer::createForItem($story, $filesystem)
            ))($response);
        } catch (ValidationException $ve) {
            $response =(new UnprocessableEntityResponse(
                $ve->getErrors()
            ))($response);
        }

        return $response;
    }
}
