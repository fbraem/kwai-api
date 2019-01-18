<?php

namespace REST\Pages\Actions;

use Interop\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Respect\Validation\Validator as v;
use Core\Validators\ValidationException;
use Core\Validators\InputValidator;

use Domain\Page\PageTransformer;
use Domain\Page\PagesTable;
use REST\Contents\ContentValidator;

use Cake\Datasource\Exception\RecordNotFoundException;

use Core\Responses\NotFoundResponse;
use Core\Responses\ResourceResponse;
use Core\Responses\UnprocessableEntityResponse;

class UpdateContentAction
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $pagesTable = PagesTable::getTableFromRegistry();
        try {
            $page = $pagesTable->get($args['id'], [
                'contain' => ['Category', 'Contents', 'Contents.User']
            ]);
        } catch (RecordNotFoundException $rnfe) {
            $response = (new NotFoundResponse(_("Page doesn't exist")))($response);
        }

        try {
            $data = $request->getParsedBody();

            (new InputValidator([
                'data.attributes.title' => v::notEmpty()->length(1, 255),
                'data.attributes.summary' => v::notEmpty(),
                'data.attributes.content' => v::notEmpty()
            ], true))->validate($data);

            $attributes = \JmesPath\search('data.attributes', $data);

            //TODO: for now we only support one content.
            // In the future we will support multi lingual
            $content = $page->contents[0];
            if (isset($attributes['title'])) {
                $content->title = $attributes['title'];
            }
            if (isset($attributes['summary'])) {
                $content->summary = $attributes['summary'];
            }
            if (isset($attributes['content'])) {
                $content->content = $attributes['content'];
            }
            if ($content->isDirty()) {
                $content->user = $request->getAttribute('clubman.user');
                $page->contents = [ $content ];
            }

            $pagesTable->save($page);

            $filesystem = $this->container->get('filesystem');

            $response = (new ResourceResponse(
                PageTransformer::createForItem($page, $filesystem)
            ))($response);
        } catch (ValidationException $ve) {
            $response = (new UnprocessableEntityResponse(
                $ve->getErrors()
            ))($response);
        }

        return $response;
    }
}
