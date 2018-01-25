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
        $db = $request->getAttribute('clubman.container')['db'];

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

        $categories = new \Domain\Category\CategoriesTable($db);
        try {
            $category = $categories->whereId($categoryId)->findOne();
        } catch (\Domain\NotFoundException $nfe) {
            return (new JSONErrorResponder(new HTTPCodeResponder(new Responder(), 422), [
                '/data/relationships/category' => [
                    _('Category doesn\'t exist')
                ]
            ]))->respond();
        }

        $attributes = \JmesPath\search('data.attributes', $data);

        $page = new \Domain\Page\Page($db, [
            'category' => $category,
            'remark' => $attributes['remark'],
            'enabled' => $attributes['enabled']
        ]);
        $page->store();

        $page->contents()->add(new \Domain\Content\Content($db, [
            'locale' => 'nl',
            'format' => 'md',
            'title' => $attributes['title'],
            'summary' => $attributes['summary'],
            'content' => $attributes['content'],
            'user' => $request->getAttribute('clubman.user')
        ]));
        $page->contents()->store();

        $payload->setOutput(new Fractal\Resource\Item($page, new \Domain\Page\PageTransformer(), 'pages'));

        return (new JSONResponder(new HTTPCodeResponder(new Responder(), 201), $payload))->respond();
    }
}
