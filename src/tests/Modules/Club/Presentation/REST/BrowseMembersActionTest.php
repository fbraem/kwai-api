<?php

declare(strict_types=1);

use Kwai\Modules\Club\Presentation\REST\BrowseMembersAction;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;

it('execute BrowseMembersAction', function () {
    $action = new BrowseMembersAction();

    $request = new ServerRequest('GET', '/club/members');
    $response = new Response();

    $action($request, $response, []);

    expect($response->getStatusCode())->toBe(200);
});
