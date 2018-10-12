<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;

use League\Plates\Engine;

use Cake\Datasource\Exception\RecordNotFoundException;

require 'src/vendor/autoload.php';

$app = \Core\Clubman::getApplication();

$app->get('/facebook/news/{id}', function (Request $request, Response $response, array $args) {
    $id = $args['id'];

    try {
        $story = \Domain\News\NewsStoriesTable::getTableFromRegistry()->get($id, [
            'contain' => ['Contents', 'Category', 'Contents.User']
        ]);
    } catch (RecordNotFoundException $rnfe) {
        return $response->withStatus(404);
    }

    $headers = $request->getHeaders();

    $facebook = false;
    $facebookUserAgent = 'facebookexternalhit/1.1';
    $userAgentStrLength = strlen($facebookUserAgent);
    $userAgents = $headers['HTTP_USER_AGENT'];
    foreach ($userAgents as $userAgent) {
        if (substr(strtolower($userAgent), 0, $userAgentStrLength) === $facebookUserAgent) {
            $facebook = true;
        }
    }

    $uri = $request->getUri();
    if ($uri->getPort() == 80) {
        $uri = $uri->withPort(null);
    }

    if ($facebook) {
        // This is the Facebook crawler ...
        $images = $this->filesystem->listContents('images/news/' . $story->id);
        $meta = [];
        $meta['og:url'] = $uri->__toString();
        $meta['og:type'] = 'article';
        $meta['og:title'] = $story->contents[0]['title'];
        $meta['og:description'] = $story->contents[0]['social_media'] ?? '';
        foreach ($images as $image) {
            if (isset($image['filename']) && $image['filename'] == 'header_detail_crop') {
                $meta['og:image'] = $uri->withPath('/files/' . $image['path'])->__toString();
                $meta['og:image:width'] = 800;
                $meta['og:image:height'] = 300;
            }
        }

        $result = $this->template->render('facebook', ['meta' => $meta]);
        $response = $response->getBody()->write($result);
    } else {
        $response = $response->withRedirect($uri->withPath('/')->__toString() . '#/news/' . $story->id);
    }

    return $response;
});

$app->run();
