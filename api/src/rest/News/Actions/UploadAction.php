<?php

namespace REST\News\Actions;

use Psr\Http\Message\RequestInterface;
use Aura\Payload\Payload;

use Core\Responders\Responder;
use Core\Responders\JSONResponder;
use Core\Responders\JSONErrorResponder;
use Core\Responders\HTTPCodeResponder;
use Core\Responders\NotFoundResponder;

use League\Fractal;

use Intervention\Image\ImageManager;

class UploadAction implements \Core\ActionInterface
{
    public function __invoke(RequestInterface $request, Payload $payload)
    {
        $files = $request->getUploadedFiles();
        if (!isset($files['image'])) {
            return new HTTPCodeResponder(new Responder(), 400);
        }
        $uploadedFilename = $files['image']->getClientFilename();

        $config = $request->getAttribute('clubman.config');

        $id = $request->getAttribute('route.id');
        $repository = new \Domain\News\NewsStoryRepository();
        $story = $repository->find($id);
        if (!$story) {
            return new NotFoundResponder(new Responder(), _("Story doesn't exist."));
        }

        $filesystem = $request->getAttribute('clubman.filesystem');
        $stream = $files['image']->getStream();
        $ext = pathinfo($uploadedFilename, PATHINFO_EXTENSION);

        $originalFilename = 'images/news/' . $id . '/header_original.' . $ext;
        $filesystem->put($originalFilename, $stream->detach());

        $originalFile = $filesystem->read($originalFilename);

        try {
            $post = $request->getParsedBody();

            $manager = new ImageManager();
            $image = $manager->make($originalFile);
            $image = $image->crop($post['overview_width'], $post['overview_height'], $post['overview_x'], $post['overview_y'])->resize($image->width() * $post['overview_scale'], $image->height() * $post['overview_scale']);
            $filesystem->put('images/news/' . $id . '/header_overview_crop.' . $ext, $image->stream());

            $image = $manager->make($originalFile);
            $image = $image->crop($post['detail_width'], $post['detail_height'], $post['detail_x'], $post['detail_y'])->resize($image->width() * $post['detail_scale'], $image->height() * $post['detail_scale']);
            $filesystem->put('images/news/' . $id . '/header_detail_crop.' . $ext, $image->stream());
        } catch (\Exception $e) {
            echo $e;
            exit;
        }

        $payload->setOutput(new Fractal\Resource\Item($story, new \Domain\News\NewsStoryTransformer($filesystem), 'news_stories'));
        return new JSONResponder(new Responder(), $payload);
    }
}
