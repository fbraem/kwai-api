<?php

namespace REST\Pages\Actions;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Aura\Payload\Payload;

use Core\Responders\Responder;
use Core\Responders\JSONResponder;
use Core\Responders\JSONErrorResponder;
use Core\Responders\HTTPCodeResponder;

class CreateContentAction implements \Core\ActionInterface
{
    public function __invoke(RequestInterface $request, Payload $payload) : ResponseInterface
    {
        $data = $payload->getInput();

        $id = $request->getAttribute('route.id');

        $pagesTable = \Domain\Page\PagesTable::getTableFromRegistry();
        try {
            $page = $pagesTable->get($id, [
                'contain' => ['Contents', 'Category', 'Contents.User']
            ]);
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $rnfe) {
            return (
                new NotFoundResponder(
                    new Responder(),
                    _("Page doesn't exist.")
                ))->respond();
        }

        $validator = new \REST\Contents\ContentValidator();
        $errors = $validator->validate($data);
        if (count($errors) > 0) {
            return (
                new JSONErrorResponder(
                    new HTTPCodeResponder(
                        new Responder(),
                        422
                    ),
                    $errors
                )
            )->respond();
        }


        $contentsTable = \Domain\Content\ContentsTable::getTableFromRegistry();
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

        $filesystem = $request->getAttribute('clubman.container')['filesystem'];
        $payload->setOutput(\Domain\Page\PageTransformer::createForItem($page, $filesystem));

        return (
            new JSONResponder(
                new HTTPCodeResponder(
                    new Responder(),
                    201
                ),
                $payload
            )
        )->respond();
    }
}
