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

use League\Fractal;

class UpdateStoryAction implements \Core\ActionInterface
{
    public function __invoke(RequestInterface $request, Payload $payload) : ResponseInterface
    {
        $id = $request->getAttribute('route.id');
        $db = $request->getAttribute('clubman.container')['db'];

        $storiesTable = new \Domain\News\NewsStoriesTable($db);
        $story = $storiesTable->whereId($id)->findOne();
        if (!$story) {
            return (new NotFoundResponder(new Responder(), _("Story doesn't exist.")))->respond();
        }

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
        $categoriesTable = new \Domain\Category\CategoriesTable($db);
        $category = $categoriesTable->whereId($categoryId)->findOne();
        if (!$category) {
            return (new JSONErrorResponder(new HTTPCodeResponder(new Responder(), 422), [
                '/data/relationships/category' => [
                    _('Category doesn\'t exist')
                ]
            ]))->respond();
        }

        $attributes = \JmesPath\search('data.attributes', $data);

        $story = new \Domain\News\NewsStory(
            $db,
            array_merge($story->extract(), [
                'category' => $category,
                'publish_date' => $attributes['publish_date'],
                'author' => $request->getAttribute('clubman.user'),
                'end_date' => $attributes['end_date'] ?? null,
                'featured' => $attributes['featured'] ?? 0,
                'featured_end_date' => $attributes['featured_end_date'] ?? null,
                'enabled' => $attributes['enabled'],
                'contents' => $story->contents(),
                'publish_date_timezone' => $attributes['publish_date_timezone'] ?? null,
                'end_date_timezone' => $attributes['end_date_timezone'] ?? null,
                'featured_date_timezone' => $attributes['featured_date_timezone'] ?? null,
            ])
        );
        $story->store();

        $content = new \Domain\Content\Content(
            $db,
            array_merge($story->contents()->contents()[0]->extract(), [
                'title' => $attributes['title'],
                'summary' => $attributes['summary'],
                'content' => $attributes['content']
            ])
        );
        $content->store();

        $story->contents()->replace($content);

        $filesystem = $request->getAttribute('clubman.container')['filesystem'];
        $payload->setOutput(new Fractal\Resource\Item($story, new \Domain\News\NewsStoryTransformer($filesystem), 'news_stories'));

        return (new JSONResponder(new HTTPCodeResponder(new Responder(), 201), $payload))->respond();
    }
}
