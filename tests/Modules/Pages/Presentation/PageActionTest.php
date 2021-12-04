<?php
declare(strict_types=1);

use Kwai\Modules\Pages\Presentation\REST\BrowsePagesAction;
use Kwai\Modules\Pages\Presentation\REST\CreatePageAction;
use Kwai\Modules\Pages\Presentation\REST\GetPageAction;
use Kwai\Modules\Pages\Presentation\REST\UpdatePageAction;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use Tests\Context;

$context = Context::createContext();

$data = <<<JSON
{
    "data": {
        "type": "stories",
        "attributes": {
            "remark": "Created with PageActionTest",
            "contents": [
                {
                    "title": "PageActionTest",
                    "summary": "This page was created with PageActionTest",
                    "content": "This page is created..."
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

it('can create a page', function () use ($context, $data) {
    $action = new CreatePageAction($context->db);

    $request = new ServerRequest(
        'POST',
        '/pages'
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

it('can update a page', function ($id) use ($context, $data) {
    $action = new UpdatePageAction($context->db);

    $json = json_decode($data, true);
    $json['data']['id'] = $id;
    $json['data']['attributes']['remark'] = 'Updated with unit test';

    $request = new ServerRequest(
        'PATCH',
        '/pages' . $id
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
    ->depends('it can create a page')
;

it('can get a page', function ($id) use ($context) {
    $action = new GetPageAction($context->db);

    $request = new ServerRequest(
        'GET',
        '/pages/' . $id
    );

    $response = new Response();
    $response = $action($request, $response, ['id' => $id]);
    expect($response->getStatusCode())->toBe(200);
})
    ->skip(!Context::hasDatabase(), 'No database available')
    ->depends('it can create a page')
;

it('can browse pages', function () use ($context) {
    $action = new BrowsePagesAction($context->db);

    $request = new ServerRequest(
        'GET',
        '/pages'
    );

    $response = new Response();
    $response = $action($request, $response, []);
    expect($response->getStatusCode())->toBe(200);
})
    ->skip(!Context::hasDatabase(), 'No database available')
;
