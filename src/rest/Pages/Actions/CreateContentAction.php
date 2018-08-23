<?php

namespace REST\Pages\Actions;

use Interop\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Domain\Page\PageTransformer;
use Domain\Page\PagesTable;
use REST\Contents\ContentValidator;
use Domain\Content\ContentsTable;

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

        $pagesTable = PagesTable::getTableFromRegistry();
        try {
            $page = $pagesTable->get($args['id'], [
                'contain' => ['Contents', 'Category', 'Contents.User']
            ]);
        } catch (RecordNotFoundException $rnfe) {
            return $response->withStatus(404, _("Page doesn't exist"));
        }

        $validator = new ContentValidator();
        if (! $validator->validate($data)) {
            return $validator->unprocessableEntityResponse($response);
        }

        $contentsTable = ContentsTable::getTableFromRegistry();
        $attributes = \JmesPath\search('data.attributes', $data);

        $content = $contentsTable->newEntity();
        $content->locale = 'nl';
        $content->format = 'md';
        $content->title = $attributes['title'];
        $content->summary = $attributes['summary'];
        $content->content = $attributes['content'];
        $content->user = $request->getAttribute('clubman.user');

        $page->contents = [ $content ];
        $pagesTable->save($page);

        $filesystem = $this->container->get('filesystem');

        return (new \Core\ResourceResponse(PageTransformer::createForItem(
            $page,
            $filesystem
        )))($response)->withStatus(201);
    }
}
