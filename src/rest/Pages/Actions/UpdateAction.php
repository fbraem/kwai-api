<?php

namespace REST\Pages\Actions;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Aura\Payload\Payload;

use Core\Responders\Responder;
use Core\Responders\JSONResponder;
use Core\Responders\JSONErrorResponder;
use Core\Responders\HTTPCodeResponder;
use Core\Responders\NotFoundResponder;

use League\Fractal;

class UpdateAction implements \Core\ActionInterface
{
    public function __invoke(RequestInterface $request, Payload $payload) : ResponseInterface
    {
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
                ))->respond();
        }

        $categoryId = \JmesPath\search('data.relationships.category.data.id', $data);
        if (isset($categoryId)) {
            try {
                $category = \Domain\Category\CategoriesTable::getTableFromRegistry()->get($categoryId);
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
        }

        $attributes = \JmesPath\search('data.attributes', $data);

        if (isset($category)) {
            $page->catgory = $category;
        }
        if (isset($attributes['priority'])) {
            $page->priority = $attributes['priority'];
        }
        if (isset($attributes['enabled'])) {
            $page->enabled = $attributes['enabled'];
        }
        if (isset($attributes['remark'])) {
            $page->remark = $attributes['remark'];
        }

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
            ))->respond();
    }
}
