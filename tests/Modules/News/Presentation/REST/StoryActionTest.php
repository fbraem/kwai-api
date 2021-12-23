<?php

declare(strict_types=1);

use Kwai\Core\Domain\ValueObjects\Timestamp;
use Kwai\Modules\News\Presentation\REST\BrowseStoriesAction;
use Kwai\Modules\News\Presentation\REST\CreateStoryAction;
use Kwai\Modules\News\Presentation\REST\GetStoryAction;
use Kwai\Modules\News\Presentation\REST\UpdateStoryAction;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use Tests\Context;

$context = Context::createContext();

$now = (string) Timestamp::createNow();
$data = <<<JSON
{
    "data": {
        "type": "stories",
        "attributes": {
            "remark": "Created with StoryActionTest",
            "timezone": "Europe/Brussels",
            "publish_date": "${now}",
            "contents": [
                {
                    "title": "StoryActionTest",
                    "summary": "This story was created with StoryActionTest"
                }
            ]
        },
        "relationships": {
            "application": {
                "data": {
                    "type": "applications",
                    "id": "1"
                }
            }
        }
    }
}
JSON;

it('can create a story', function () use ($context, $data) {
    $action = new CreateStoryAction($context->db);

    $request = new ServerRequest(
        'POST',
        '/news/stories'
    );
    $request = $request
        ->withParsedBody(json_decode($data, true))
        ->withAttribute('kwai.user', $context->user)
    ;
    $response = new Response();

    $response = $action($request, $response, []);
    expect($response->getStatusCode())->toBe(200);

    $stream = $response->getBody();
    $stream->rewind();
    $json = json_decode($stream->getContents());
    return $json->data->id;
})
    ->skip(!Context::hasDatabase(), 'No database available')
;

it('can update a story', function ($id) use ($context, $data) {
    $action = new UpdateStoryAction($context->db);

    $json = json_decode($data, true);
    $json['data']['id'] = $id;
    $json['data']['attributes']['remark'] = 'Updated with unit test';

    $request = new ServerRequest(
        'PATCH',
        '/news/stories/' . $id
    );

    $request = $request
        ->withParsedBody($json)
        ->withAttribute(
            'kwai.user',
            $context->user
        )
    ;
    $response = new Response();

    $response = $action($request, $response, ['id' => $id]);
    expect($response->getStatusCode())->toBe(200);
})
    ->skip(!Context::hasDatabase(), 'No database available')
    ->depends('it can create a story')
;

it('can get a story', function ($id) use ($context) {
    $action = new GetStoryAction($context->db);

    $request = new ServerRequest(
        'GET',
        '/news/stories/' . $id
    );

    $response = new Response();
    $response = $action($request, $response, [ 'id' => $id]);
    expect($response->getStatusCode())->toBe(200);
})
    ->skip(!Context::hasDatabase(), 'No database available')
    ->depends('it can create a story')
;

it('can browse stories', function () use ($context) {
    $action = new BrowseStoriesAction($context->db);

    $request = new ServerRequest(
        'GET',
        '/news/stories'
    );

    $response = new Response();
    $response = $action($request, $response, []);
    expect($response->getStatusCode())->toBe(200);
})
    ->skip(!Context::hasDatabase(), 'No database available')
;
