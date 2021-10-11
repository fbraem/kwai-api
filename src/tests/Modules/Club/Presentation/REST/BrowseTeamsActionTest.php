<?php

declare(strict_types=1);

use Kwai\Modules\Club\Presentation\REST\BrowseTeamsAction;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;

it('execute BrowseTeamsAction', function () {
    $action = new BrowseTeamsAction();

    $request = new ServerRequest('GET', '/club/teams');
    $response = new Response();

    $response = $action($request, $response, []);

    expect($response->getStatusCode())->toBe(200);
});
