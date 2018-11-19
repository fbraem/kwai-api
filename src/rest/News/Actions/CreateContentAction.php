<?php

namespace REST\News\Actions;

use Interop\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Domain\News\NewsStoryTransformer;
use Domain\News\NewsStoriesTable;

use Domain\Content\ContentsTable;

use Cake\Datasource\Exception\RecordNotFoundException;

use Respect\Validation\Validator as v;

use Core\Validators\ValidationException;
use Core\Validators\InputValidator;

use Core\Responses\NotFoundResponse;
use Core\Responses\ResourceResponse;
use Core\Responses\UnprocessableEntityResponse;

class CreateContentAction
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $data = $request->getParsedBody();

        $storiesTable = NewsStoriesTable::getTableFromRegistry();
        try {
            $story = $storiesTable->get($args['id'], [
                'contain' => ['Contents', 'Category', 'Contents.User']
            ]);
        } catch (RecordNotFoundException $rnfe) {
            return (new NotFoundResponse(_("Story doesn't exist")))($response);
        }

        try {
            (new InputValidator([
                'data.attributes.title' => v::notEmpty()->length(1, 255),
                'data.attributes.content' => v::notEmpty()
            ]))->validate($data);

            $contentsTable = ContentsTable::getTableFromRegistry();
            $attributes = \JmesPath\search('data.attributes', $data);

            // If it already exists, we update
            // TODO: check when multiple languages are possible!
            if ($page->contents) {
                $content = $page->contents[0];
            } else {
                $content = $contentsTable->newEntity();
            }
            $content->locale = 'nl';
            $content->format = 'md';
            $content->title = $attributes['title'];
            $content->summary = $attributes['summary'];
            $content->content = $attributes['content'];
            $content->user = $request->getAttribute('clubman.user');

            $story->contents = [ $content ];
            $storiesTable->save($story);

            $route = $request->getAttribute('route');
            if (! empty($route)) {
                $route->setArgument('id', $story->contents[0]->id);
            }

            $filesystem = $this->container->get('filesystem');

            $connection->commit();

            $response = (new ResourceResponse(
                NewsStoryTransformer::createForItem($story, $filesystem)
            ))($response)->withStatus(201);
        } catch (ValidationException $ve) {
            $response = (new UnprocessableEntityResponse(
                $ve->getErrors()
            ))($response);
        }

        return $response;
    }
}
