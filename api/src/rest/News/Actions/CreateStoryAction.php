<?php

namespace REST\News\Actions;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Aura\Payload\Payload;

use Core\Responders\Responder;
use Core\Responders\JSONResponder;
use Core\Responders\JSONErrorResponder;
use Core\Responders\HTTPCodeResponder;

use League\Fractal;

class CreateStoryAction implements \Core\ActionInterface
{
    public function __invoke(RequestInterface $request, Payload $payload) : ResponseInterface
    {
        $db = $request->getAttribute('clubman.container')['db'];

        $data = $payload->getInput();

        $validator = new \REST\News\NewsStoryValidator();
        $errors = $validator->validate($data);
        if (count($errors) > 0) {
            return (new JSONErrorResponder(new HTTPCodeResponder(new Responder(), 422), $errors))->respond();
        }

        $categoryId = \JmesPath\search('data.relationships.category.data.id', $data);
        if (!isset($categoryId)) {
            return (new JSONErrorResponder(new HTTPCodeResponder(new Responder(), 422), [
                '/data/relationships/category' => [
                    _('Category is required')
                ]
            ]))->respond();
        }
        $categories = new \Domain\News\NewsCategoriesTable($db);
        $category = $categories->whereId($categoryId)->findOne();
        if (!$category) {
            return (new JSONErrorResponder(new HTTPCodeResponder(new Responder(), 422), [
                '/data/relationships/category' => [
                    _('Category doesn\'t exist')
                ]
            ]))->respond();
        }

        $attributes = \JmesPath\search('data.attributes', $data);

        $publishDate = new \Carbon\Carbon($attributes['publish_date']);

        $story = new \Domain\News\NewsStory($db, [
            'category' => $category,
            'author' => $request->getAttribute('clubman.user'),
            'publish_date' => $publishDate->toDateTimeString(),
            'end_date' => $attributes['end_date'] ?? null,
            'featured' => $attributes['featured'] ?? 0,
            'featured_end_date' => $attributes['featured_end_date'] ?? null,
            'enabled' => $attributes['enabled']
        ]);
        $story->store();

        $story->contents()->add(new \Domain\Content\Content($db, [
            'locale' => 'nl',
            'format' => 'md',
            'title' => $attributes['title'],
            'summary' => $attributes['summary'],
            'content' => $attributes['content'],
            'user' => $request->getAttribute('clubman.user')
        ]));
        $story->contents()->store();

        $payload->setOutput(new Fractal\Resource\Item($story, new \Domain\News\NewsStoryTransformer(), 'news_stories'));

        return (new JSONResponder(new HTTPCodeResponder(new Responder(), 201), $payload))->respond();
    }
}
