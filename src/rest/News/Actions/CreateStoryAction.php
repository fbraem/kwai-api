<?php

namespace REST\News\Actions;

use Psr\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Respect\Validation\Validator as v;

use Core\Validators\ValidationException;
use Core\Validators\InputValidator;
use Core\Validators\EntityExistValidator;

use Core\Responses\UnprocessableEntityResponse;
use Core\Responses\ResourceResponse;

use Domain\News\NewsStoryTransformer;
use Domain\News\NewsStoriesTable;
use Domain\Content\ContentsTable;

class CreateStoryAction
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $data = $request->getParsedBody();

        try {
            (new InputValidator([
                'data.attributes.featured' => v::digit(),
                'data.attributes.featured_end_date' => [ v::date('Y-m-d H:i:s'), true ],
                'data.attributes.publish_date' => v::date('Y-m-d H:i:s'),
                'data.attributes.timezone' => v::notEmpty()->length(1, 255),
                'data.attributes.end_date' => [ v::date('Y-m-d H:i:s'), true ],
                'data.attributes.enabled' => v::boolType()
            ]))->validate($data);

            $storiesTable = NewsStoriesTable::getTableFromRegistry();

            $category = (new EntityExistValidator('data.relationships.category', $storiesTable->Category, true))->validate($data);

            $attributes = \JmesPath\search('data.attributes', $data);

            $story = $storiesTable->newEntity();
            $story->category = $category;
            $story->publish_date = $attributes['publish_date'];
            $story->timezone = $attributes['timezone'] ?? null;
            $story->end_date = $attributes['end_date'] ?? null;
            $story->featured = $attributes['featured'] ?? 0;
            $story->featured_end_date = $attributes['featured_end_date'] ?? null;
            $story->enabled = $attributes['enabled'];
            $story->remark = $attributes['remark'] ?? null;

            $content = ContentsTable::getTableFromRegistry()->newEntity();
            $story->contents = [ $content ];
            $content->title = $attributes['contents'][0]['title'];
            $content->locale = $attributes['contents'][0]['locale'] ?? 'nl';
            $content->format = $attributes['contents'][0]['format'] ?? 'md';
            $content->summary = $attributes['contents'][0]['summary'];
            $content->content = $attributes['contents'][0]['content'];
            $content->user = $request->getAttribute('clubman.user');

            $storiesTable->save($story);

            $route = $request->getAttribute('route');
            if (! empty($route)) {
                $route->setArgument('id', $story->id);
            }

            $filesystem = $this->container->get('filesystem');

            return (new ResourceResponse(
                NewsStoryTransformer::createForItem($story, $filesystem)
            ))($response)->withStatus(201);
        } catch (ValidationException $ve) {
            return (new UnprocessableEntityResponse(
                $ve->getErrors()
            ))($response);
        }
    }
}
