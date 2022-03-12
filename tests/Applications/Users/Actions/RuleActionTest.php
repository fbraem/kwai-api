<?php
declare(strict_types=1);

use Kwai\Applications\Users\Actions\BrowseRulesAction;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use Tests\DatabaseTrait;

uses(DatabaseTrait::class);
beforeEach()->withDatabase();

it('can browse rules', function () {
    $action = new BrowseRulesAction($this->db);

    $request = new ServerRequest(
        'GET',
        '/users/rules'
    );
    $response = new Response();

    $response = $action($request, $response, []);
    expect($response->getStatusCode())->toBe(200);
})
    ->skip(fn () => !$this->hasDatabase(), 'No database available')
;
