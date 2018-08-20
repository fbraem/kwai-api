<?php

namespace REST\Pages\Actions;

use Interop\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use League\Fractal\Manager;
use League\Fractal\Serializer\JsonApiSerializer;

use Domain\Page\PageTransformer;
use Domain\Page\PagesTable;
use REST\Contents\ContentValidator;

use Cake\Datasource\Exception\RecordNotFoundException;

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
            return $response.withStatus(404, _("Page doesn't exist"));
        }

        $data = $request->getParsedBody();
        $validator = new ContentValidator();
        if (! $validator->validate($data)) {
            return $response
                ->withStatus(422)
                ->withHeader('content-type', 'application/vnd.api+json')
                ->getBody()
                ->write($validator->toJSON());
        }

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
        $resource = PageTransformer::createForItem($page, $filesystem);

        $fractal = new Manager();
        $fractal->setSerializer(new JsonApiSerializer(/*$this->baseURL*/));
        $data = $fractal->createData($resource)->toJson();

        return $response
            ->withHeader('content-type', 'application/vnd.api+json')
            ->getBody()
            ->write($data);
    }
}
