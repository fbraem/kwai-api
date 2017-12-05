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
        $data = $payload->getInput();

        $validator = new \Domain\News\NewsStoryValidator();
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
        $categoryRepository = new \Domain\News\NewsCategoryRepository();
        $category = $categoryRepository->find($categoryId);
        if ($category == null) {
            return (new JSONErrorResponder(new HTTPCodeResponder(new Responder(), 422), [
                '/data/relationships/category' => [
                    _('Category doesn\'t exist')
                ]
            ]))->respond();
        }

        $attributes = \JmesPath\search('data.attributes', $data);

        $repository = new \Domain\News\NewsStoryRepository();
        $story = new \Domain\News\NewsStory();
        $story->category = $category;
        $story->publish_date = $attributes['publish_date'];
        $story->user_id = $request->getAttribute('clubman.user');

        $content = new \Domain\Content\Content();
        $content->locale = 'nl';
        $content->format = 'html';
        $content->title = $attributes['title'];
        $content->summary = $attributes['summary'];
        $content->content = $attributes['content'];
        $story->contents->add($content);
        $repository->store($story);

        $payload->setOutput(new Fractal\Resource\Item($story, new \Domain\News\NewsStoryTransformer(), 'news_stories'));

        return (new JSONResponder(new HTTPCodeResponder(new Responder(), 201), $payload))->respond();
    }
}
