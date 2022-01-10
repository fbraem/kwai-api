<?php
declare(strict_types=1);

use Kwai\Modules\Users\Presentation\REST\BrowseUserInvitationsAction;
use Kwai\Modules\Users\Presentation\REST\ConfirmInvitationAction;
use Kwai\Modules\Users\Presentation\REST\CreateUserInvitationAction;
use Kwai\Modules\Users\Presentation\REST\GetUserInvitationAction;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use Tests\Context;

$context = Context::createContext();

$data = [
    'data' => [
        'type' => 'user_invitations',
        'attributes' => [
            'email' => "jigor.kano" . strval(rand(1, 100)) . "@gmail.com",
            'name' => 'Jigoro Kano',
            'remark' => 'A user invitation created with a unittest'
        ]
    ]
];

it('can create an user invitation', function () use ($context, $data) {
    $action = new CreateUserInvitationAction($context->db);

    $request = new ServerRequest(
        'POST',
        '/users/invitations/',
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
    return $result['data']['attributes']['uuid'];
})
    ->skip(!Context::hasDatabase(), 'No database available')
;

it('can get an user invitation', function ($uuid) use ($context) {
    $action = new GetUserInvitationAction($context->db);

    $request = new ServerRequest(
        'GET',
        '/users/invitations/' . $uuid
    );
    $response = new Response();

    $response = $action($request, $response, ['uuid' => $uuid]);
    expect($response->getStatusCode())->toBe(200);
})
    ->skip(!Context::hasDatabase(), 'No database available')
    ->depends('it can create an user invitation')
;

it('can confirm a user invitation', function ($uuid) use ($context, $data) {
    $action = new ConfirmInvitationAction($context->db);
    unset($data['data']['attributes']['name']);
    $data['data']['attributes']['first_name'] = 'Jigoro';
    $data['data']['attributes']['last_name'] = 'Kano';
    $data['data']['attributes']['password'] = 'Test1234';

    $request = new ServerRequest(
        'PATCH',
        '/users/invitations/' . $uuid
    );
    $request = $request->withParsedBody($data);

    $response = new Response();

    $response = $action($request, $response, ['uuid' => $uuid]);
    expect($response->getStatusCode())->toBe(200);
})
    ->skip(!Context::hasDatabase(), 'No database available')
    ->depends('it can create an user invitation')
;

it('can browse user invitations', function () use ($context) {
    $action = new BrowseUserInvitationsAction($context->db);

    $request = new ServerRequest(
        'GET',
        '/users/invitations/'
    );
    $response = new Response();

    $response = $action($request, $response, []);
    expect($response->getStatusCode())->toBe(200);
})
    ->skip(!Context::hasDatabase(), 'No database available')
;
