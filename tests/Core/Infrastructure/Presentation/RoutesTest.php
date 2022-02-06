<?php
declare(strict_types=1);

use Kwai\Applications\Users\UsersApplication;
use Nyholm\Psr7\ServerRequest;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;

it('can load routes from attributes', function () {
    $app = new UsersApplication();
    $routes = $app->createRoutes();

    $request = new ServerRequest(
       'GET',
       '/users'
    );
    $symfonyRequest = (new HttpFoundationFactory())->createRequest($request);
    $context = new RequestContext();
    $context->fromRequest($symfonyRequest);
    $matcher = new UrlMatcher($routes, $context);
    $parameters = $matcher->matchRequest($symfonyRequest);
    expect($routes)->toBeObject();
    expect($parameters)
        ->toBeArray()
        ->toHaveKeys([
            'auth',
            '_action',
            '_route',
        ])
        ->and($parameters['_route'])
        ->toEqual('users.get')
    ;
});
