<?php
declare(strict_types=1);

use Kwai\Applications\Users\Actions\BrowseUserAccountsAction;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use Tests\Context;

$context = Context::createContext();

it('can browse users', function () use ($context) {
    $action = new BrowseUserAccountsAction($context->db);

    $request = new ServerRequest(
        'GET',
        '/users/accounts'
    );
    $response = new Response();

    $response = $action($request, $response, []);
    expect($response->getStatusCode())->toBe(200);

    $result = json_decode((string) $response->getBody(), true);
    return $result['data'][0]['id'];
})
    ->skip(!Context::hasDatabase(), 'No database available')
;
