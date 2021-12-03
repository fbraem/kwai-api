<?php

declare(strict_types=1);

use Kwai\Modules\Club\Presentation\REST\BrowseMembersAction;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use Tests\Context;

$context = Context::createContext();

it('execute BrowseMembersAction', function () use ($context) {
    $action = new BrowseMembersAction($context->db);

    $request = new ServerRequest('GET', '/club/members');
    $response = new Response();

    $response = $action($request, $response, []);

    expect($response->getStatusCode())->toBe(200);
})
    ->skip(!Context::hasDatabase(), 'No database available')
;
