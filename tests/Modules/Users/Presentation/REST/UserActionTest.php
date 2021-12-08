<?php
declare(strict_types=1);

use Kwai\Modules\Users\Presentation\REST\BrowseUsersAction;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use Tests\Context;

$context = Context::createContext();

it('can browse users', function () use ($context) {
    $action = new BrowseUsersAction($context->db);

    $request = new ServerRequest(
        'GET',
        '/users'
    );
    $response = new Response();

    $response = $action($request, $response, []);
    expect($response->getStatusCode())->toBe(200);

    $result = json_decode((string) $response->getBody(), true);
    return $result['data'][0]['attributes']['uuid'];
})
    ->skip(!Context::hasDatabase(), 'No database available')
;

it('can get a user', function ($uuid) use ($context) {
    $action = new BrowseUsersAction($context->db);

    $request = new ServerRequest(
        'GET',
        '/users/' . $uuid
    );
    $response = new Response();

    $response = $action($request, $response, ['uuid' => $uuid]);
    expect($response->getStatusCode())->toBe(200);
})
    ->skip(!Context::hasDatabase(), 'No database available')
    ->depends('it can browse users')
;
