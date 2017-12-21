<?php

namespace REST\News\Actions;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Aura\Payload\Payload;

use Core\Responders\Responder;
use Core\Responders\JSONResponder;
use Core\Responders\JSONErrorResponder;
use Core\Responders\HTTPCodeResponder;

use League\Fractal;

class CreateCategoryAction implements \Core\ActionInterface
{
    public function __invoke(RequestInterface $request, Payload $payload) : ResponseInterface
    {
        $data = $payload->getInput();

        $validator = new \REST\News\NewsCategoryValidator();
        $errors = $validator->validate($data);
        if (count($errors) > 0) {
            return (new JSONErrorResponder(new HTTPCodeResponder(new Responder(), 422), $errors))->respond();
        }

        $attributes = \JmesPath\search('data.attributes', $data);

        $db = $request->getAttribute('clubman.container')['db'];
        $attributes['user'] = $request->getAttribute('clubman.user');

        $category = new \Domain\News\NewsCategory($db, $attributes);
        $category->store();

        $payload->setOutput(new Fractal\Resource\Item($category, new \Domain\News\NewsCategoryTransformer(), 'news_categories'));

        return (new JSONResponder(new HTTPCodeResponder(new Responder(), 201), $payload))->respond();
    }
}
