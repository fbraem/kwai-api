<?php

namespace REST\Pages\Actions;

use Interop\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Domain\Page\PageTransformer;
use Domain\Page\PagesTable;
use Domain\Content\ContentsTable;

use Core\Responses\UnprocessableEntityResponse;
use Core\Responses\ResourceResponse;
use Core\Responses\NotFoundResponse;

use Respect\Validation\Validator as v;
use Core\Validators\ValidationException;
use Core\Validators\InputValidator;

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

        try {
            $pagesTable = PagesTable::getTableFromRegistry();
            $page = $pagesTable->get($args['id'], [
                'contain' => ['Contents', 'Category', 'Contents.User']
            ]);

            (new InputValidator([
                'data.attributes.title' => v::notEmpty()->length(1, 255),
                'data.attributes.summary' => v::notEmpty(),
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

            $page->contents = [ $content ];
            $pagesTable->save($page);

            $route = $request->getAttribute('route');
            if (! empty($route)) {
                $route->setArgument('id', $page->contents[0]->id);
            }

            $filesystem = $this->container->get('filesystem');

            $response = (new ResourceResponse(PageTransformer::createForItem(
                $page,
                $filesystem
                )))($response)->withStatus(201);
        } catch (RecordNotFoundException $rnfe) {
            $response = (new NotFoundResponse(
                _("Page doesn't exist")
            ))($response);
        } catch (ValidationException $ve) {
            $response = (new UnprocessableEntityResponse(
                $ve->getErrors()
            ))($response);
        }

        return $response;
    }
}
