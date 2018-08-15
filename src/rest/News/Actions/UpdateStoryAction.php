<?php

namespace REST\News\Actions;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Aura\Payload\Payload;

use Core\Responders\Responder;
use Core\Responders\JSONResponder;
use Core\Responders\JSONErrorResponder;
use Core\Responders\HTTPCodeResponder;
use Core\Responders\NotFoundResponder;

class UpdateStoryAction implements \Core\ActionInterface
{
    public function __invoke(RequestInterface $request, Payload $payload) : ResponseInterface
    {
        $id = $request->getAttribute('route.id');

        $storiesTable = \Domain\News\NewsStoriesTable::getTableFromRegistry();
        try {
            $story = $storiesTable->get($id, [
                'contain' => ['Category']
            ]);
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $rnfe) {
            return (
                new NotFoundResponder(
                    new Responder(),
                    _("Story doesn't exist.")
                ))->respond();
        }

        $data = $payload->getInput();

        $validator = new \REST\News\NewsStoryValidator();
        $errors = $validator->validate($data);
        if (count($errors) > 0) {
            return (
                new JSONErrorResponder(
                    new HTTPCodeResponder(
                        new Responder(),
                        422
                    ),
                $errors
                )
            )->respond();
        }

        $categoryId = \JmesPath\search('data.relationships.category.data.id', $data);
        if (isset($categoryId)) {
            try {
                $category = \Domain\Category\CategoriesTable::getTableFromRegistry()->get($categoryId);
            } catch (\Cake\Datasource\Exception\RecordNotFoundException $rnfe) {
                return (
                    new JSONErrorResponder(
                        new HTTPCodeResponder(
                            new Responder(),
                            422
                        ),
                        [
                            '/data/relationships/category' => [
                                _('Category doesn\'t exist')
                            ]
                        ]
                    ))->respond();
            }
        }

        $attributes = \JmesPath\search('data.attributes', $data);

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
        if (isset($attributes['publish_date_timezone'])) {
            $story->publish_date_timezone = $attributes['publish_date_timezone'];
        }
        if (isset($attributes['end_date_timezone'])) {
            $story->end_date_timezone = $attributes['end_date_timezone'];
        }
        if (isset($attributes['featured_date_timezone'])) {
            $story->featured_date_timezone = $attributes['featured_date_timezone'];
        }
        if (isset($attributes['remark'])) {
            $story->remark = $attributes['remark'];
        }

        $storiesTable->save($story);

        $filesystem = $request->getAttribute('clubman.container')['filesystem'];
        $payload->setOutput(\Domain\News\NewsStoryTransformer::createForItem($story, $filesystem));

        return (
            new JSONResponder(
                new HTTPCodeResponder(
                    new Responder(),
                    200
                ),
                $payload
            )
        )->respond();
    }
}
