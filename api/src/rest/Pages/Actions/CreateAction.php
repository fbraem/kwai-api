<?php

namespace REST\Pages\Actions;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Aura\Payload\Payload;

use Core\Responders\Responder;
use Core\Responders\JSONResponder;
use Core\Responders\JSONErrorResponder;
use Core\Responders\HTTPCodeResponder;

use League\Fractal;

class CreateAction implements \Core\ActionInterface
{
    public function __invoke(RequestInterface $request, Payload $payload) : ResponseInterface
    {
        $data = $payload->getInput();

        $validator = new \REST\Pages\PageValidator();
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

        $categoryId = \JmesPath\search('data.relationships.category.data.id', $data);
        if (!isset($categoryId)) {
            return (
                new JSONErrorResponder(
                    new HTTPCodeResponder(
                        new Responder(),
                        422
                    ),
                    [
                        '/data/relationships/category' => [
                            _('Category is required')
                        ]
                    ]
                )
            )->respond();
        }

        $pagesTable = \Domain\Page\PagesTable::getTableFromRegistry();
        try {
            $category = $pagesTable->Category->get($categoryId);
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $rnfe) {
            return (
                new JSONErrorResponder(
                    new HTTPCodeResponder(
                        new Responder(),
                        422
                    ),
                    [
                        '/data/relationships/category' => [
                            _('Category doesn\'t exist')
                        ]
                    ]
                ))->respond();
        }

        $attributes = \JmesPath\search('data.attributes', $data);

        $page = $pagesTable->newEntity();
        $page->category = $category;
        $page->remark = $attributes['remark'];
        $page->enabled = $attributes['enabled'];
        $page->priority = $attributes['priority'];

        $contentsTable = \Domain\Content\ContentsTable::getTableFromRegistry();
        $content = $contentsTable->newEntity();
        $content->locale = 'nl';
        $content->format = 'md';
        $content->title = $attributes['title'];
        $content->summary = $attributes['summary'];
        $content->content = $attributes['content'];
        $content->user = $request->getAttribute('clubman.user');
        $page->contents = [ $content ];

        $pagesTable->save($page);

        $payload->setOutput(\Domain\Page\PageTransformer::createForItem($page));

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
