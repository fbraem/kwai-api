<?php
declare(strict_types=1);

use Kwai\Modules\Users\Presentation\REST\BrowseRulesAction;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use Tests\Context;

$context = Context::createContext();

it('can browse rules', function () use ($context) {
    $action = new BrowseRulesAction($context->db);

    $request = new ServerRequest(
        'GET',
        '/users/rules'
    );
    $response = new Response();

    $response = $action($request, $response, []);
    expect($response->getStatusCode())->toBe(200);
})
    ->skip(!Context::hasDatabase(), 'No database available')
;
