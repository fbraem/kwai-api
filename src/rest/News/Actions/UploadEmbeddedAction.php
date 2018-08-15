<?php

namespace REST\News\Actions;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Aura\Payload\Payload;

use Core\Responders\Responder;
use Core\Responders\SimpleJSONResponder;
use Core\Responders\HTTPCodeResponder;
use Core\Responders\NotFoundResponder;

use League\Fractal;

class UploadEmbeddedAction implements \Core\ActionInterface
{
    public function __invoke(RequestInterface $request, Payload $payload) : ResponseInterface
    {
        $files = $request->getUploadedFiles();
        if (!isset($files['image'])) {
            return (new HTTPCodeResponder(new Responder(), 400))->respond();
        }
        $uploadedFilename = $files['image']->getClientFilename();

        $config = $request->getAttribute('clubman.config');

        $id = $request->getAttribute('route.id');
        $repository = new \Domain\News\NewsStoryRepository();
        $story = $repository->find($id);
        if (!$story) {
            return (new NotFoundResponder(new Responder(), _("Story doesn't exist.")))->respond();
        }

        $filesystem = $request->getAttribute('clubman.container')['filesystem'];
        $stream = $files['image']->getStream();

        $originalFilename = 'images/news/' . $id . '/' . $uploadedFilename;
        $filesystem->put($originalFilename, $stream->detach());

        //TODO: Remove the /files/ url part
        return (new SimpleJSONResponder(new Responder(), [
            'url' => '/files/' . $originalFilename
        ]))->respond();
    }
}
