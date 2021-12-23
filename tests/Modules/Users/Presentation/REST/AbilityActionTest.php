<?php
declare(strict_types=1);

use Kwai\Modules\Users\Presentation\REST\BrowseAbilitiesAction;
use Kwai\Modules\Users\Presentation\REST\CreateAbilityAction;
use Kwai\Modules\Users\Presentation\REST\GetAbilityAction;
use Kwai\Modules\Users\Presentation\REST\UpdateAbilityAction;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use Tests\Context;

$context = Context::createContext();

$data = [
    'data' => [
        'type' => 'abilities',
        'attributes' => [
            'name' => 'Unittest Ability',
            'remark' => 'Created with a unit test'
        ]
    ]
];

it('can create an ability', function () use ($context, $data) {
    $action = new CreateAbilityAction($context->db);

    $request = new ServerRequest(
        'PATCH',
        '/users/abilities/',
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

it('can update an ability', function ($id) use ($context, $data) {
    $action = new UpdateAbilityAction($context->db);

    $data['data']['id'] = $id;
    $data['data']['attributes']['remark'] = 'Updated with unit test';

    $request = new ServerRequest(
        'PATCH',
        '/users/abilities/' . $id
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
    ->depends('it can create an ability')
;

it('can get an ability', function ($id) use ($context) {
    $action = new GetAbilityAction($context->db);

    $request = new ServerRequest(
        'GET',
        '/users/abilities/' . $id
    );
    $response = new Response();

    $response = $action($request, $response, ['id' => $id]);
    expect($response->getStatusCode())->toBe(200);
})
    ->skip(!Context::hasDatabase(), 'No database available')
    ->depends('it can create an ability')
;

it('can browse abilities', function () use ($context) {
    $action = new BrowseAbilitiesAction($context->db);

    $request = new ServerRequest(
        'GET',
        '/users/abilities'
    );
    $response = new Response();

    $response = $action($request, $response, []);
    expect($response->getStatusCode())->toBe(200);
})
    ->skip(!Context::hasDatabase(), 'No database available')
;
