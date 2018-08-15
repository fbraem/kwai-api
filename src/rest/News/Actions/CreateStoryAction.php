<?php

namespace REST\News\Actions;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Aura\Payload\Payload;

use Core\Responders\Responder;
use Core\Responders\JSONResponder;
use Core\Responders\JSONErrorResponder;
use Core\Responders\HTTPCodeResponder;

class CreateStoryAction implements \Core\ActionInterface
{
    public function __invoke(RequestInterface $request, Payload $payload) : ResponseInterface
    {
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
        if (!isset($categoryId)) {
            return (
                new JSONErrorResponder(
                    new HTTPCodeResponder(
                        new Responder(),
                        422
                    ),
                    [
                        '/data/relationships/category' => [
                            _('Category is required')
                        ]
                    ]
                )
            )->respond();
        }

        $storiesTable = \Domain\News\NewsStoriesTable::getTableFromRegistry();
        try {
            $category = $storiesTable->Category->get($categoryId);
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

        $attributes = \JmesPath\search('data.attributes', $data);

        $story = $storiesTable->newEntity();
        $story->category = $category;
        $story->publish_date = $attributes['publish_date'];
        $story->publish_date_timezone = $attributes['publish_date_timezone'] ?? null;
        $story->end_date = $attributes['end_date'] ?? null;
        $story->end_date_timezone = $attributes['end_date_timezone'] ?? null;
        $story->featured = $attributes['featured'] ?? 0;
        $story->featured_end_date = $attributes['featured_end_date'] ?? null;
        $story->featered_end_date_timezone = $attributes['featured_end_date_timezone'] ?? null;
        $story->enabled = $attributes['enabled'];
        $story->remark = $attributes['remark'] ?? null;

        $storiesTable->save($story);
        $filesystem = $request->getAttribute('clubman.container')['filesystem'];
        $payload->setOutput(\Domain\News\NewsStoryTransformer::createForItem($story, $filesystem));

        return (
            new JSONResponder(
                new HTTPCodeResponder(
                    new Responder(),
                    201
                ),
                $payload
            )
        )->respond();
    }
}
