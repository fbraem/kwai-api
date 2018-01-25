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
        $db = $request->getAttribute('clubman.container')['db'];

        $pagesTable = new \Domain\Page\PagesTable($db);
        try {
            $page = $pagesTable->whereId($id)->findOne();
        } catch (\Domain\NotFoundException $nfe) {
            return (new NotFoundResponder(new Responder(), _("Page doesn't exist.")))->respond();
        }

        $data = $payload->getInput();

        $validator = new \REST\Pages\PageValidator();
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
        $categoriesTable = new \Domain\Category\CategoriesTable($db);
        try {
            $category = $categoriesTable->whereId($categoryId)->findOne();
        } catch (\Domain\NotFoundException $nfe) {
            return (new JSONErrorResponder(new HTTPCodeResponder(new Responder(), 422), [
                    '/data/relationships/category' => [
                        _('Category doesn\'t exist')
                    ]
                ]))->respond();
        }

        $attributes = \JmesPath\search('data.attributes', $data);

        $page = new \Domain\Page\Page(
            $db,
            array_merge($page->extract(), [
                'category' => $category,
                'enabled' => $attributes['enabled'],
                'contents' => $page->contents(),
                'remark' => $attributes['remark'] ?? null,
            ])
        );
        $page->store();

        $content = new \Domain\Content\Content(
            $db,
            array_merge($page->contents()->contents()[0]->extract(), [
                'user' => $request->getAttribute('clubman.user'),
                'title' => $attributes['title'],
                'summary' => $attributes['summary'],
                'content' => $attributes['content']
            ])
        );
        $content->store();

        $page->contents()->replace($content);

        $filesystem = $request->getAttribute('clubman.container')['filesystem'];
        $payload->setOutput(new Fractal\Resource\Item($page, new \Domain\Page\PageTransformer($filesystem), 'pages'));

        return (new JSONResponder(new HTTPCodeResponder(new Responder(), 201), $payload))->respond();
    }
}
