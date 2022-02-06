<?php
declare(strict_types=1);

use Kwai\Applications\Users\Actions\BrowseRolesAction;
use Kwai\Applications\Users\Actions\CreateRoleAction;
use Kwai\Applications\Users\Actions\GetRoleAction;
use Kwai\Applications\Users\Actions\UpdateRoleAction;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use Tests\Context;

$context = Context::createContext();

$data = [
    'data' => [
        'type' => 'roles',
        'attributes' => [
            'name' => 'Unittest Role',
            'remark' => 'Created with a unit test'
        ]
    ]
];

it('can create a role', function () use ($context, $data) {
    $action = new CreateRoleAction($context->db);

    $request = new ServerRequest(
        'PATCH',
        '/users/roles/',
        []
    );
    $request = $request
        ->withParsedBody($data)
        ->withAttribute(
            'kwai.user',
            $context->user
        )
    ;
    $response = new Response();

    $response = $action($request, $response, []);
    expect($response->getStatusCode())->toBe(200);

    $result = json_decode((string) $response->getBody(), true);
    return $result['data']['id'];
})
    ->skip(!Context::hasDatabase(), 'No database available')
;

it('can update a role', function ($id) use ($context, $data) {
    $action = new UpdateRoleAction($context->db);

    $data['data']['id'] = $id;
    $data['data']['attributes']['remark'] = 'Updated with unit test';

    $request = new ServerRequest(
        'PATCH',
        '/users/roles/' . $id
    );
    $request = $request
        ->withParsedBody($data)
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
    ->depends('it can create a role')
;

it('can get a role', function ($id) use ($context) {
    $action = new GetRoleAction($context->db);

    $request = new ServerRequest(
        'GET',
        '/users/roles/' . $id
    );
    $response = new Response();

    $response = $action($request, $response, ['id' => $id]);
    expect($response->getStatusCode())->toBe(200);
})
    ->skip(!Context::hasDatabase(), 'No database available')
    ->depends('it can create a role')
;

it('can browse roles', function () use ($context) {
    $action = new BrowseRolesAction($context->db);

    $request = new ServerRequest(
        'GET',
        '/users/roles'
    );
    $response = new Response();

    $response = $action($request, $response, []);
    expect($response->getStatusCode())->toBe(200);
})
    ->skip(!Context::hasDatabase(), 'No database available')
;
