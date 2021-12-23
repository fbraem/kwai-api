<?php

declare(strict_types=1);

use Kwai\Modules\Coaches\Presentation\REST\BrowseCoachesAction;
use Kwai\Modules\Coaches\Presentation\REST\CreateCoachAction;
use Kwai\Modules\Coaches\Presentation\REST\GetCoachAction;
use Kwai\Modules\Coaches\Presentation\REST\UpdateCoachAction;
use Nyholm\Psr7\ServerRequest;
use Nyholm\Psr7\Response;
use Tests\Context;

$context = Context::createContext();

// TODO: Make sure that there is a member and make sure the coach isn't
// already linked to that member...
$data = <<<JSON
{
    "data": {
        "type": "coaches",
        "attributes": {
            "active": false,
            "diploma": "No diploma",
            "bio": "This coach is created in a test",
            "remark": "Created with CoachActionTest"
        },
        "relationships": {
            "member": {
                "data": {
                    "type": "members",
                    "id": "1"
                }
            }
        }
    }
}
JSON;

it('can create coach', function () use ($context, $data) {
    $action = new CreateCoachAction($context->db);
    $request = new ServerRequest(
        'POST',
        '/coaches'
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

it('can update a coach', function ($id) use ($context, $data) {
    $action = new UpdateCoachAction($context->db);

    $json = json_decode($data, true);
    $json['data']['id'] = $id;
    $json['data']['attributes']['remark'] = 'Updated with unit test';

    $request = new ServerRequest(
        'PATCH',
        '/coaches/' . $id
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
    ->depends('it can create a coach')
;

it('can get a coach', function ($id) use ($context) {
    $action = new GetCoachAction($context->db);

    $request = new ServerRequest(
        'GET',
        '/coaches/' . $id
    );

    $response = new Response();
    $response = $action($request, $response, [ 'id' => $id]);
    expect($response->getStatusCode())->toBe(200);
})
    ->skip(!Context::hasDatabase(), 'No database available')
    ->depends('it can create a coach')
;

it('can browse coaches', function () use ($context) {
    $action = new BrowseCoachesAction($context->db);

    $request = new ServerRequest(
        'GET',
        '/coaches'
    );

    $response = new Response();
    $response = $action($request, $response, []);
    expect($response->getStatusCode())->toBe(200);
})
    ->skip(!Context::hasDatabase(), 'No database available')
;
