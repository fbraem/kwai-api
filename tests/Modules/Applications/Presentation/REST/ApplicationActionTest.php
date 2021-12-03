<?php

declare(strict_types=1);

use Kwai\Modules\Applications\Presentation\REST\GetApplicationAction;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use Tests\Context;

$context = Context::createContext();

// TODO: add create/update application test

it('can get an application', function () use ($context) {
    $action = new GetApplicationAction($context->db);

    $request = new ServerRequest(
        'GET',
        '/applications/1'
    );

    $response = new Response();
    $response = $action($request, $response, ['id' => '1']);
    expect($response->getStatusCode())->toBe(200);
})
    ->skip(!Context::hasDatabase(), 'No database available')
;
