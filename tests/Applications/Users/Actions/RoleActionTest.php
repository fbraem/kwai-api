<?php
declare(strict_types=1);

use Kwai\Applications\Users\Actions\BrowseRolesAction;
use Kwai\Applications\Users\Actions\CreateRoleAction;
use Kwai\Applications\Users\Actions\GetRoleAction;
use Kwai\Applications\Users\Actions\UpdateRoleAction;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use Tests\DatabaseTrait;

uses(DatabaseTrait::class);
beforeEach()->withDatabase();


$data = [
    'data' => [
        'type' => 'roles',
        'attributes' => [
            'name' => 'Unittest Role',
            'remark' => 'Created with a unit test'
        ]
    ]
];

it('can create a role', function () use ($data) {
    $action = new CreateRoleAction($this->db);

    $request = new ServerRequest(
        'PATCH',
        '/users/roles/',
        []
    );
    $request = $request
        ->withParsedBody($data)
        ->withAttribute(
            'kwai.user',
            $this->withUser()
        )
    ;
    $response = new Response();

    $response = $action($request, $response, []);
    expect($response->getStatusCode())->toBe(200);

    $result = json_decode((string) $response->getBody(), true);
    return $result['data']['id'];
})
    ->skip(fn () => !$this->hasDatabase(), 'No database available')
;


it('can update a role', function ($id) use ($data) {
    $action = new UpdateRoleAction($this->db);

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
            $this->withUser()
        )
    ;

    $response = new Response();
    $response = $action($request, $response, ['id' => $id]);
    expect($response->getStatusCode())->toBe(200);
})
    ->depends('it can create a role')
    ->skip(fn () => !$this->hasDatabase(), 'Skipped, no database available')
;

it('can get a role', function ($id) {
    $action = new GetRoleAction($this->db);

    $request = new ServerRequest(
        'GET',
        '/users/roles/' . $id
    );
    $response = new Response();

    $response = $action($request, $response, ['id' => $id]);
    expect($response->getStatusCode())->toBe(200);
})
    ->depends('it can create a role')
    ->skip(fn () => !$this->hasDatabase(), 'Skipped, no database available')
;

it('can browse roles', function () {
    $action = new BrowseRolesAction($this->db);

    $request = new ServerRequest(
        'GET',
        '/users/roles'
    );
    $response = new Response();

    $response = $action($request, $response, []);
    expect($response->getStatusCode())->toBe(200);
})
    ->skip(fn () => !$this->hasDatabase(), 'Skipped, no database available')
;
