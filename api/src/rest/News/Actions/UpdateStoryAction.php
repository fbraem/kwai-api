<?php

namespace REST\News\Actions;

use Psr\Http\Message\RequestInterface;
use Aura\Payload\Payload;

use Core\Responders\Responder;
use Core\Responders\JSONResponder;
use Core\Responders\JSONErrorResponder;
use Core\Responders\HTTPCodeResponder;
use Core\Responders\NotFoundResponder;

use League\Fractal;

class UpdateStoryAction implements \Core\ActionInterface
{
    public function __invoke(RequestInterface $request, Payload $payload)
    {
        $id = $request->getAttribute('route.id');
        $repository = new \Domain\News\NewsStoryRepository();
        $story = $repository->find($id);
        if (!$story) {
            return new NotFoundResponder(new Responder(), _("Story doesn't exist."));
        }

        $data = $payload->getInput();

        $validator = new \Domain\News\NewsStoryValidator();
        $errors = $validator->validate($data);
        if (count($errors) > 0) {
            return new JSONErrorResponder(new HTTPCodeResponder(new Responder(), 422), $errors);
        }

        $categoryId = \JmesPath\search('data.relationships.category.data.id', $data);
        if (!isset($categoryId)) {
            return new JSONErrorResponder(new HTTPCodeResponder(new Responder(), 422), [
                '/data/relationships/category' => [
                    _('Category is required')
                ]
            ]);
        }
        $categoryRepository = new \Domain\News\NewsCategoryRepository();
        $category = $categoryRepository->find($categoryId);
        if ($category == null) {
            return new JSONErrorResponder(new HTTPCodeResponder(new Responder(), 422), [
                '/data/relationships/category' => [
                    _('Category doesn\'t exist')
                ]
            ]);
        }

        $attributes = \JmesPath\search('data.attributes', $data);

        $repository = new \Domain\News\NewsStoryRepository();
        $story->title = $attributes['title'];
        $story->category = $category;
        $story->summary = $attributes['summary'];
        $story->content = $attributes['content'];
        $story->publish_date = $attributes['publish_date'];
        $story->user_id = $request->getAttribute('clubman.user');
        $repository->store($story);

        $payload->setOutput(new Fractal\Resource\Item($story, new \Domain\News\NewsStoryTransformer, 'news_story'));

        return new JSONResponder(new HTTPCodeResponder(new Responder(), 201), $payload);
    }
}
