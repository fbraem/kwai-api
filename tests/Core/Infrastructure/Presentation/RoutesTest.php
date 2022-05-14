<?php
declare(strict_types=1);

use Kwai\Applications\Users\UsersApplication;
use Kwai\Core\Infrastructure\Presentation\RouteClassLoader;
use Nyholm\Psr7\ServerRequest;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;

it('can load routes from attributes', function () {
    $app = new UsersApplication();

    $request = new ServerRequest(
       'GET',
       '/users'
    );

    $loader = new RouteClassLoader();
    $routes = $loader->loadAll($app->getActions());

    $symfonyRequest = (new HttpFoundationFactory())->createRequest($request);
    $context = new RequestContext();
    $context->fromRequest($symfonyRequest);
    $matcher = new UrlMatcher($routes, $context);
    $parameters = $matcher->matchRequest($symfonyRequest);
    expect($routes)->toBeObject();
    expect($parameters)
        ->toBeArray()
        ->toHaveKeys([
            '_extra',
            '_action',
            '_route',
        ])
        ->and($parameters['_route'])
        ->toEqual('users.browse')
    ;
});
