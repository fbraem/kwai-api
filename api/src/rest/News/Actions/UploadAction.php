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

class UploadAction implements \Core\ActionInterface
{
    public function __invoke(RequestInterface $request, Payload $payload)
    {
        $files = $request->getUploadedFiles();
        if (!isset($files['image'])) {
            return new HTTPCodeResponder(new Responder(), 400);
        }

        $config = $request->getAttribute('clubman.config');
        $path = $config->files . 'images/news/';
        if (! file_exists($path)) {
            echo 'creating ...';
            echo var_dump(mkdir($path, 0755, true));
        }
        else {
            echo 'Already exist';
        }
        $filename = $path . $files['image']->getClientFilename();
        echo $filename;
        if (file_exists($filename)) {
            echo 'Already exist 2';
        }
        try {
            echo $files['image']->moveTo($filename);
        } catch (\Exception $e) {
            echo $e;
            exit;
        }
        exit;
/*
        list($width, $height) = getimagesize($filename);
        if ($width == null && $height == null) { // This file is not an image
            unlink($filename);
            $this->payload->setMessages([
                _('Invalid file uploaded')
            ]);
            return new \Core\Responders\ErrorResponder($this->payload);
        }

        $this->payload->setOutput(new \Core\Collection([
            'url' => substr($filename, strlen(CLUBMAN_ABSPATH))
        ]));
*/
        return new \Core\Responders\JSONResponder($this->payload);
        //return new JSONResponder(new HTTPCodeResponder(new Responder(), 201), $payload);
    }
}
